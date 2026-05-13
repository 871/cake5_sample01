<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Cake\Mail\MailsRepository;

use App\Domain\Mail\Entity\Mail as DomainEntity;
use App\Domain\Mail\ValueObject as Vo;
use App\Infrastructure\Persistence\Cake\Mail\MailMapper;
use App\Model\Entity\Mail\Mail;
use App\Model\Table\Mail\MailReceivedCheckLogsTable;
use App\Model\Table\Mail\MailsTable;
use Cake\Core\Configure;
use Cake\Log\Log;
use Cake\ORM\Locator\LocatorAwareTrait;
use Cake\Utility\Text;
use DateTimeImmutable;
use RuntimeException;
use Throwable;
use Webklex\PHPIMAP\ClientManager;
use Webklex\PHPIMAP\Message;

final class CheckReceived
{
    use LocatorAwareTrait;

    public const LIMIT = 50;

    /**
     * @var \App\Model\Table\Mail\MailsTable
     */
    private MailsTable $table;

    /**
     * @var \App\Model\Table\Mail\MailReceivedCheckLogsTable
     */
    private MailReceivedCheckLogsTable $receivedCheckLogsTable;

    /**
     * 処理件数
     *
     * @var int
     */
    private int $processed;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->table = $this->fetchTable(MailsTable::class);
        $this->receivedCheckLogsTable = $this->fetchTable(MailReceivedCheckLogsTable::class);
        $this->processed = 0;
    }

    /**
     * @param \DateTimeImmutable $now
     * @return int
     */
    public function run(DateTimeImmutable $now): int
    {
        /** @var array<\App\Domain\Mail\Entity\Mail> $mails */
        $mails = $this->findTargetMails($now);
        if ($mails === []) {
            return $this->processed;
        }

        foreach ($mails as $mail) {
            $this->checkReceivedMail($mail, $now);
        }

        return $this->run($now);
    }

    /**
     * @param \DateTimeImmutable $now
     * @return array<\App\Domain\Mail\Entity\Mail>
     */
    private function findTargetMails(DateTimeImmutable $now): array
    {
        static $offset = 0;
        /** @var array<\App\Model\Entity\Mail\Mail> $mails */
        $rows = $this->table->find()
            ->where([
                'Mails.send_status' => Vo\SendStatus::SENT,
                // Memo: 送信予定日時が1ヶ月以上前のメールは処理対象外とする
                // （何らかの理由で受信確認が行われていない古いメールが大量に存在することを防ぐため）
                'Mails.send_scheduled_at >=' => $now->modify('-1 month')->format('Y-m-d\TH:i:s'),
            ])
            ->orderBy([
                'Mails.send_scheduled_at' => 'ASC',
                'Mails.id' => 'ASC',
            ])
            ->offset($offset)
            ->limit(self::LIMIT)
            ->all()
            ->toArray();

        $offset = $offset + self::LIMIT;

        return array_map(
            static fn(Mail $mail): DomainEntity => (new MailMapper())->toDomainEntity($mail),
            $rows,
        );
    }

    /**
     * メールの受信を確認し、受信ログ保存とステータス更新を行う
     *
     * @param \App\Domain\Mail\Entity\Mail $entity
     * @param \DateTimeImmutable $now 現在日時
     */
    private function checkReceivedMail(DomainEntity $entity, DateTimeImmutable $now): void
    {
        try {
            $message = $this->mailReceived($entity);

            $this->saveMailReceivedCheckSuccess($message, $entity, $now);

            $this->processed++;
        } catch (RuntimeException $e) {
            Log::warning($e->getMessage());
            // 受信できない状況が続いている可能性があるため、次回以降の処理で再度確認する
        } catch (Throwable $e) {
            Log::error('受信確認処理中に予期せぬエラーが発生しました。' . $e->getMessage());
        }
    }

    /**
     * メールの受信を確認を行う
     *
     * @param \App\Domain\Mail\Entity\Mail $entity
     * @return \Webklex\PHPIMAP\Message
     */
    private function mailReceived(DomainEntity $entity): Message
    {
        $client = (new ClientManager())->make((array)Configure::read('PHPIMAP.received_check'));
        $client->connect();

        $folder = $client->getFolder('INBOX');
        /** @var \Webklex\PHPIMAP\Message $message|null */
        $message = $folder
            ->query()
            ->whereHeader('X-mail_id', $entity->id()->toString())
            ->whereHeader('X-mail_send_scheduled_at', $entity->sendScheduledAt()->toString())
            ->whereHeader('X-related_data_key', $entity->relatedDataKey()->toString())
            ->get()
            ->first();

        $client->disconnect();

        return $message ?? throw new RuntimeException(
            '受信確認対象のメールが見つかりませんでした。'
            . '[メールID: ' . $entity->id()->toString() . ']'
            . '[送信予定日時: ' . $entity->sendScheduledAt()->toString() . ']'
            . '[関連データキー: ' . $entity->relatedDataKey()->toString() . ']',
        );
    }

    /**
     * @param \Webklex\PHPIMAP\Message $message
     * @param \App\Domain\Mail\Entity\Mail $entity
     * @param \DateTimeImmutable $now
     * @return void
     */
    private function saveMailReceivedCheckSuccess(
        Message $message,
        DomainEntity $entity,
        DateTimeImmutable $now,
    ): void {
        $this->table->getConnection()->transactional(
            function () use ($message, $entity, $now): void {
                $mail = $this->table->get($entity->id()->toString());
                $this->table->patchEntity($mail, [
                    'send_status' => Vo\SendStatus::RECEIVED,
                    'modified' => $now->format('Y-m-d\TH:i:s'),
                    'modified_by' => null,
                    'modified_ip' => null,
                ], [
                    'validate' => false,
                ]);
                $this->table->saveOrFail($mail, [
                    'checkExisting' => false,
                ]);

                $log = $this->receivedCheckLogsTable->newEntity([
                    'id' => Text::uuid(),
                    'mail_id' => $entity->id()->toString(),
                    'original_message_id' => $message->getMessageId(),
                    'checked_address' => $message->getFrom()[0]->mail,
                    'checked_at' => $now->format('Y-m-d\TH:i:s'),
                    'created' => $now->format('Y-m-d\TH:i:s'),
                    'created_by' => null,
                    'created_ip' => null,
                ], [
                    'validate' => false,
                ]);
                $this->receivedCheckLogsTable->saveOrFail($log, [
                    'checkExisting' => false,
                ]);
            },
        );
    }
}
