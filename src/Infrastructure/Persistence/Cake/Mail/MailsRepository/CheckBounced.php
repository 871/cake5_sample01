<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Cake\Mail\MailsRepository;

use App\Domain\Mail\ValueObject as Vo;
use App\Domain\Mail\Entity\Mail as DomainEntity;
use App\Infrastructure\Persistence\Cake\Mail\MailMapper;
use App\Model\Entity\Mail\Mail;
use App\Model\Table\Mail\MailBounceLogsTable;
use App\Model\Table\Mail\MailsTable;
use Cake\Core\Configure;
use Cake\Log\Log;
use Cake\ORM\Locator\LocatorAwareTrait;
use Cake\Utility\Text;
use DateTimeImmutable;
use DateTimeInterface;
use Webklex\PHPIMAP\ClientManager;
use Webklex\PHPIMAP\Client;
use Webklex\PHPIMAP\Message;

final class CheckBounced
{
    use LocatorAwareTrait;

    const LIMIT = 50;

    private const DEFAULT_BOUNCE_TYPE = 'UNKNOWN';
    private const DEFAULT_BOUNCED_EMAIL = 'unknown@example.com';

    /**
     * @var \App\Model\Table\Mail\MailsTable
     */
    private MailsTable $table;

    /**
     * @var \App\Model\Table\Mail\MailBounceLogsTable
     */
    private MailBounceLogsTable $bounceLogsTable;

    /**
     * @var array<\Webklex\PHPIMAP\Message>
     */
    private array $messages;

    /**
     * 処理件数
     * @var int
     */
    private int $processed;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->table = $this->fetchTable(MailsTable::class);
        $this->bounceLogsTable = $this->fetchTable(MailBounceLogsTable::class);
        $this->processed = 0;
    }

    /**
     * @param \DateTimeImmutable $now
     * @return int
     */
    public function run(DateTimeImmutable $now): int
    {
        // 対象バウンスドメール取得
        $this->messages = $this->findTargetBouncedMessages(
            threshold_bounced_at: $this->findMaxMailBounceLogsBouncedAt(),
        );

        return $this->_run($now);
    }

    /**
     * @return DateTimeInterface
     */
    private function findMaxMailBounceLogsBouncedAt(): DateTimeInterface
    {
        /** @var object|null $latest */
        $latest = $this->bounceLogsTable->find()
            ->select(['bounced_at'])
            ->orderBy(['bounced_at' => 'DESC'])
            ->first()
            ?->toArray() ?? [
                'bounced_at' => new DateTimeImmutable('@0'),
            ];

        return $latest['bounced_at'];
    }

    /**
     * @param \DateTimeInterface $threshold_bounced_at
     * @return array<\Webklex\PHPIMAP\Message>
     */
    private function findTargetBouncedMessages(
        DateTimeInterface $threshold_bounced_at
    ): array {
        $client = (new ClientManager())->make((array)Configure::read('PHPIMAP.return_path'));
        $client->connect();
        
        // TODO 未実装 $threshold_bounced_atの指定日付を超過したメールを取得する

        $client->disconnect();

        return $messages;
    }

    /**
     * @param \DateTimeImmutable $now
     * @return int
     */
    public function _run(DateTimeImmutable $now): int
    {
         /** @var array<\App\Domain\Mail\Entity\Mail> $mails */
        $mails = $this->findTargetMails($now);
        if ($mails === []) {
            return $this->processed;
        }

        foreach ($mails as $mail) {
            $this->processBouncedMail($mail, $now);
        }

        return $this->_run($now);
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
                'Mails.send_status IN' => [
                    Vo\SendStatus::SENT,
                    Vo\SendStatus::RECEIVED,
                ],
                // Memo: 送信予定日時が2週以上前のメールは処理対象外とする
                'Mails.send_scheduled_at >=' => $now->modify('-2 weeks')->format('Y-m-d\TH:i:s'),
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
            static fn(Mail $mail): DomainEntity =>  (new MailMapper())->toDomainEntity($mail),
            $rows,
        );
    }

    /**
     * メールのバウンスを確認し、バウンスログ保存とステータス更新を行う
     * @param \App\Domain\Mail\Entity\Mail $entity
     * @param \DateTimeImmutable $now 現在日時
     */
    private function processBouncedMail(DomainEntity $entity, DateTimeImmutable $now): void
    {
        try {
            $message = $this->getBouncedMessage($entity);
            if ($message === null) {
                return;
            }

            $this->saveMailBouncedLog($message, $entity, $now);

            $this->processed++;
        } catch (\Throwable $e) {
            Log::error('バウンスメール確認処理中に予期せぬエラーが発生しました。' . $e->getMessage());
        }
    }

    /**
     * @param \App\Domain\Mail\Entity\Mail $entity
     * @return ?\Webklex\PHPIMAP\Message
     */
    private function getBouncedMessage(DomainEntity $entity): ?Message
    {
        // TODO 未実装 メールのバウンスを確認を行う
        // $entityの内容から、$this->messagesの中から該当するバウンスメールを特定して返す
        // 特定できない場合はnullを返す
        // 優先順位は以下の順とする
        // 1. X-mail_id, X-mail_send_scheduled_at, X-related_data
        // 2. Original-Message-ID
        // 3. In-Reply-To
        // 4. References
        // 5. 添付message/rfc822
        // ※条件ごとにpriovateメソッドを作成し、順番に呼び出していく形で実装すること
        // 例: 
        // return $this->findBouncedMessageByXHeaders($entity) 
        //     ?? $this->findBouncedMessageByOriginalMessageId($entity) 
        //     ?? ...;
    
        return null;
    }

    /**
     * @param \Webklex\PHPIMAP\Message $message
     * @param \App\Domain\Mail\Entity\Mail $entity
     * @param \DateTimeImmutable $now
     * @return void
     */
    private function saveMailBouncedLog(Message $message, DomainEntity $entity, DateTimeImmutable $now): void
    {
        // TODO 未実装 メールのバウンスログ保存とステータス更新を行う
        // トランザクション内で以下の処理を行う
        // 1. Mailsテーブルの該当メールのsend_statusをBOUNCEDに更新する
        // 2. MailBounceLogsテーブルにバウンスログを保存する
        // テーブル情報はマイグレーションのmail_bounce_logsのCREATE TABLE文を参照
        
     }
}
