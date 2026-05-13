<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Cake\Mail\MailsRepository;

use App\Domain\Mail\Entity\Mail as DomainEntity;
use App\Domain\Mail\ValueObject as Vo;
use App\Infrastructure\Persistence\Cake\Mail\MailMapper;
use App\Lib\UUID\UUID;
use App\Model\Entity\Mail\Mail;
use App\Model\Table\Mail\MailSentLogsTable;
use App\Model\Table\Mail\MailsTable;
use Cake\Mailer\Mailer;
use Cake\ORM\Locator\LocatorAwareTrait;
use DateTimeImmutable;
use Throwable;

final class SendWaiting
{
    use LocatorAwareTrait;

    private const SEND_LIMIT = 50;

    /**
     * @var \App\Model\Table\Mail\MailsTable
     */
    private MailsTable $table;

    /**
     * @var \App\Model\Table\Mail\MailSentLogsTable
     */
    private MailSentLogsTable $sentLogsTable;

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
        $this->sentLogsTable = $this->fetchTable(MailSentLogsTable::class);
        $this->processed = 0;
    }

    /**
     * 送信待ちメールを送信し、送信ログ保存とステータス更新を行う
     *
     * @param \DateTimeImmutable $now 現在日時
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
            $this->sendMail($mail, $now);
            $this->processed++;
        }

        return $this->run($now);
    }

    /**
     * 送信対象のメールを取得する
     *
     * @param \DateTimeImmutable $now 現在日時
     * @return array<\App\Domain\Mail\Entity\Mail>
     */
    private function findTargetMails(DateTimeImmutable $now): array
    {
        /** @var array<\App\Model\Entity\Mail\Mail> $rows */
        $rows = $this->table->find()
            ->where([
                'Mails.send_status' => Vo\SendStatus::WAITING,
                'Mails.send_scheduled_at <=' => $now->format('Y-m-d\TH:i:s'),
            ])
            ->orderBy([
                'Mails.send_scheduled_at' => 'ASC',
                'Mails.id' => 'ASC',
            ])
            ->limit(self::SEND_LIMIT) // 一度に大量のメールを送信しないようにするため、上限を設ける
            ->all()
            ->toArray();

        return array_map(
            static fn(Mail $mail): DomainEntity => (new MailMapper())->toDomainEntity($mail),
            $rows,
        );
    }

    /**
     * メールを送信し、送信ログ保存とステータス更新を行う
     *
     * @param \App\Domain\Mail\Entity\Mail $entity
     * @param \DateTimeImmutable $now 現在日時
     */
    private function sendMail(DomainEntity $entity, DateTimeImmutable $now): void
    {
        try {
            $mailer = $this->mailSent($entity);

            $this->saveMailSentSuccess($mailer, $entity, $now);
        } catch (Throwable $e) {
            $this->saveMailSentFailed($entity, $now, $e);
        }
    }

    /**
     * メール送信処理
     *
     * @param \App\Domain\Mail\Entity\Mail $entity
     * @return \Cake\Mailer\Mailer
     */
    private function mailSent(DomainEntity $entity): Mailer
    {
        $mailer = new Mailer('default');
        $mailer
            ->setTo($entity->mailTo()->toArray())
            ->setCc($entity->mailCc()->toArray())
            ->setBcc($entity->mailBcc()->toArray() + [
                $entity->mailReceivedCheck()->toString(),
            ])
            ->setReturnPath($entity->mailReturnPath()->toString())
            ->setEmailFormat('text')
            ->setHeaders([
                'X-mail_id' => $entity->id()->toString(),
                'X-mail_send_scheduled_at' => $entity->sendScheduledAt()->toString(),
                'X-related_data_key' => $entity->relatedDataKey()->toString(),
            ])
            ->setSubject($entity->title()->toString())
            ->deliver($entity->body()->toString());

        return $mailer;
    }

    /**
     * @param \Cake\Mailer\Mailer $mailer
     * @param \App\Domain\Mail\Entity\Mail $entity
     * @param \DateTimeImmutable $now
     * @return void
     */
    private function saveMailSentSuccess(
        Mailer $mailer,
        DomainEntity $entity,
        DateTimeImmutable $now,
    ): void {
        $messageId = (string)$mailer->getMessage()->getMessageId();

        $this->table->getConnection()->transactional(
            function () use ($entity, $now, $messageId): void {
                // メール送信成功のステータスに更新
                $mail = $this->table->get($entity->id()->toString());
                $this->table->patchEntity($mail, [
                    'send_status' => Vo\SendStatus::SENT,
                    'original_message_id' => $messageId,
                    'modified' => $now->format('Y-m-d\TH:i:s'),
                    'modified_by' => null,
                    'modified_ip' => null,
                ], [
                    'validate' => false,
                ]);
                $this->table->saveOrFail($mail, [
                    'checkExisting' => false,
                ]);

                // 送信ログ保存
                $sentLog = $this->sentLogsTable->newEntity([
                    'id' => UUID::uuid7(),
                    'mail_id' => $entity->id()->toString(),
                    'original_message_id' => $messageId,
                    'send_status' => Vo\MailSentLogs\SendStatus::SENT,
                    'error_message' => null,
                    'sent_at' => $now->format('Y-m-d\TH:i:s'),
                    'created' => $now->format('Y-m-d\TH:i:s'),
                    'created_by' => null,
                    'created_ip' => null,
                ], [
                    'validate' => false,
                ]);
                $this->sentLogsTable->saveOrFail($sentLog, [
                    'checkExisting' => false,
                ]);
            },
        );
    }

    /**
     * @param \App\Domain\Mail\Entity\Mail $entity
     * @param \DateTimeImmutable $now
     * @param \Throwable $e
     * @return void
     */
    private function saveMailSentFailed(
        DomainEntity $entity,
        DateTimeImmutable $now,
        Throwable $e,
    ): void {
        $this->table->getConnection()->transactional(
            function () use ($entity, $now, $e): void {
                // メール送信成功のステータスに更新
                $mail = $this->table->get($entity->id()->toString());
                $this->table->patchEntity($mail, [
                    'send_status' => Vo\SendStatus::FAILED,
                    'original_message_id' => null,
                    'modified' => $now->format('Y-m-d\TH:i:s'),
                    'modified_by' => null,
                    'modified_ip' => null,
                ], [
                    'validate' => false,
                ]);
                $this->table->saveOrFail($mail, [
                    'checkExisting' => false,
                ]);

                // 送信ログ保存
                $sentLog = $this->sentLogsTable->newEntity([
                    'id' => UUID::uuid7(),
                    'mail_id' => $entity->id()->toString(),
                    'original_message_id' => null,
                    'send_status' => Vo\MailSentLogs\SendStatus::FAILED,
                    'error_message' => mb_substr($e->getMessage(), 0, 65535),
                    'sent_at' => $now->format('Y-m-d\TH:i:s'),
                    'created' => $now->format('Y-m-d\TH:i:s'),
                    'created_by' => null,
                    'created_ip' => null,
                ], [
                    'validate' => false,
                ]);
                $this->sentLogsTable->saveOrFail($sentLog, [
                    'checkExisting' => false,
                ]);
            },
        );
    }
}
