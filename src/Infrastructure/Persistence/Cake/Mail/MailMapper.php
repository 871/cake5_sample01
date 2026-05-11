<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Cake\Mail;

use App\Domain\Mail\Entity\Mail as DomainEntity;
use App\Domain\Mail\Entity\MailBounceLog as DomainBounceLogEntity;
use App\Domain\Mail\Entity\MailReceivedCheckLog as DomainReceivedCheckLogEntity;
use App\Domain\Mail\Entity\MailSentLog as DomainSentLogEntity;
use App\Model\Entity\Mail\Mail as OrmEntity;
use App\Model\Entity\Mail\MailBounceLog as OrmBounceLogEntity;
use App\Model\Entity\Mail\MailReceivedCheckLog as OrmReceivedCheckLogEntity;
use App\Model\Entity\Mail\MailSentLog as OrmSentLogEntity;
use App\Model\Table\Mail\MailBounceLogsTable;
use App\Model\Table\Mail\MailReceivedCheckLogsTable;
use App\Model\Table\Mail\MailSentLogsTable;
use App\Model\Table\Mail\MailsTable;
use App\Security\Input\Cast;
use App\Security\Input\StrictCast;
use Cake\ORM\Locator\LocatorAwareTrait;

final class MailMapper
{
    use LocatorAwareTrait;

    /**
     * @var \App\Model\Table\Mail\MailsTable
     */
    private MailsTable $table;

    /**
     * @var \App\Model\Table\Mail\MailSentLogsTable
     */
    private MailSentLogsTable $sentLogsTable;

    /**
     * @var \App\Model\Table\Mail\MailReceivedCheckLogsTable
     */
    private MailReceivedCheckLogsTable $receivedCheckLogsTable;

    /**
     * @var \App\Model\Table\Mail\MailBounceLogsTable
     */
    private MailBounceLogsTable $bounceLogsTable;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->table = $this->fetchTable(MailsTable::class);
        $this->sentLogsTable = $this->fetchTable(MailSentLogsTable::class);
        $this->receivedCheckLogsTable = $this->fetchTable(MailReceivedCheckLogsTable::class);
        $this->bounceLogsTable = $this->fetchTable(MailBounceLogsTable::class);
    }

    /**
     * @param \App\Domain\Mail\Entity\Mail $domainEntity
     * @return \App\Model\Entity\Mail\Mail
     */
    public function toNewOrmEntity(DomainEntity $domainEntity): OrmEntity
    {
        /** @var \App\Model\Entity\Mail\Mail */
        return $this->table->newEntity([
            'id' => $domainEntity->id()->toIntOrNull(),
            'related_data_key' => $domainEntity->relatedDataKey()->toString(),
            'send_status' => $domainEntity->sendStatus()->toString(),
            'send_scheduled_at' => $domainEntity->sendScheduledAt()->format('Y-m-d\TH:i:s'),
            'title' => $domainEntity->title()->toString(),
            'body' => $domainEntity->body()->toString(),
            'mail_to' => $domainEntity->mailTo()->toString(),
            'mail_cc' => $domainEntity->mailCc()->toStringOrNull(),
            'mail_bcc' => $domainEntity->mailBcc()->toStringOrNull(),
            'mail_received_check' => $domainEntity->mailReceivedCheck()->toString(),
            'mail_return_path' => $domainEntity->mailReturnPath()->toString(),
            'created' => $domainEntity->created()->format('Y-m-d\TH:i:s'),
            'created_by' => $domainEntity->createdBy()->toIntOrNull(),
            'created_ip' => $domainEntity->createdIp()->toStringOrNull(),
            'modified' => $domainEntity->modified()->format('Y-m-d\TH:i:s'),
            'modified_by' => $domainEntity->modifiedBy()->toIntOrNull(),
            'modified_ip' => $domainEntity->modifiedIp()->toStringOrNull(),
        ], [
            'validate' => false,
        ]);
    }

    /**
     * @param \App\Model\Entity\Mail\Mail $ormEntity
     * @return \App\Domain\Mail\Entity\Mail
     */
    public function toDomainEntity(OrmEntity $ormEntity): DomainEntity
    {
        return new DomainEntity(
            id: StrictCast::toString($ormEntity->id),
            related_data_key: StrictCast::toString($ormEntity->related_data_key),
            send_status: StrictCast::toString($ormEntity->send_status),
            send_scheduled_at: StrictCast::toString($ormEntity->send_scheduled_at->format('Y-m-d\TH:i:s')),
            title: StrictCast::toString($ormEntity->title),
            body: StrictCast::toString($ormEntity->body),
            mail_to: StrictCast::toString($ormEntity->mail_to),
            mail_cc: Cast::toStringOrNull($ormEntity->mail_cc),
            mail_bcc: Cast::toStringOrNull($ormEntity->mail_bcc),
            mail_received_check: StrictCast::toString($ormEntity->mail_received_check),
            mail_return_path: StrictCast::toString($ormEntity->mail_return_path),
            created: StrictCast::toString($ormEntity->created->format('Y-m-d\TH:i:s')),
            created_by: Cast::toStringOrNull($ormEntity->created_by),
            created_ip: Cast::toStringOrNull($ormEntity->created_ip),
            modified: StrictCast::toString($ormEntity->modified->format('Y-m-d\TH:i:s')),
            modified_by: Cast::toStringOrNull($ormEntity->modified_by),
            modified_ip: Cast::toStringOrNull($ormEntity->modified_ip),
            mail_sent_logs: $ormEntity->mail_sent_logs !== null
                ? array_map(
                    fn(OrmSentLogEntity $log): DomainSentLogEntity => $this->toDomainSentLogEntity($log),
                    $ormEntity->mail_sent_logs,
                )
                : [],
            mail_received_check_logs: $ormEntity->mail_received_check_logs !== null
                ? array_map(
                    function (OrmReceivedCheckLogEntity $log): DomainReceivedCheckLogEntity {
                        return $this->toDomainReceivedCheckLogEntity($log);
                    },
                    $ormEntity->mail_received_check_logs,
                )
                : [],
            mail_bounce_logs: $ormEntity->mail_bounce_logs !== null
                ? array_map(
                    fn(OrmBounceLogEntity $log): DomainBounceLogEntity => $this->toDomainBounceLogEntity($log),
                    $ormEntity->mail_bounce_logs,
                )
                : [],
        );
    }

    /**
     * @param \App\Domain\Mail\Entity\MailSentLog $domainEntity
     * @return \App\Model\Entity\Mail\MailSentLog
     */
    public function toNewOrmSentLogEntity(DomainSentLogEntity $domainEntity): OrmSentLogEntity
    {
        /** @var \App\Model\Entity\Mail\MailSentLog */
        return $this->sentLogsTable->newEntity([
            'id' => $domainEntity->id()->toString(),
            'mail_id' => $domainEntity->mailId()->toInt(),
            'original_message_id' => null,
            'send_status' => $domainEntity->sendStatus()->toString(),
            'error_message' => $domainEntity->errorMessage()->toStringOrNull(),
            'sent_at' => $domainEntity->sentAt()->format('Y-m-d\TH:i:s'),
            'created' => $domainEntity->created()->format('Y-m-d\TH:i:s'),
            'created_by' => $domainEntity->createdBy()->toIntOrNull(),
            'created_ip' => $domainEntity->createdIp()->toStringOrNull(),
        ], [
            'validate' => false,
        ]);
    }

    /**
     * @param \App\Model\Entity\Mail\MailSentLog $ormEntity
     * @return \App\Domain\Mail\Entity\MailSentLog
     */
    public function toDomainSentLogEntity(OrmSentLogEntity $ormEntity): DomainSentLogEntity
    {
        return new DomainSentLogEntity(
            id: StrictCast::toString($ormEntity->id),
            mail_id: StrictCast::toString($ormEntity->mail_id),
            send_status: StrictCast::toString($ormEntity->send_status),
            error_message: Cast::toStringOrNull($ormEntity->error_message),
            sent_at: StrictCast::toString($ormEntity->sent_at->format('Y-m-d\TH:i:s')),
            created: StrictCast::toString($ormEntity->created->format('Y-m-d\TH:i:s')),
            created_by: Cast::toStringOrNull($ormEntity->created_by),
            created_ip: Cast::toStringOrNull($ormEntity->created_ip),
        );
    }

    /**
     * @param \App\Domain\Mail\Entity\MailReceivedCheckLog $domainEntity
     * @return \App\Model\Entity\Mail\MailReceivedCheckLog
     */
    public function toNewOrmReceivedCheckLogEntity(
        DomainReceivedCheckLogEntity $domainEntity,
    ): OrmReceivedCheckLogEntity {
        /** @var \App\Model\Entity\Mail\MailReceivedCheckLog */
        return $this->receivedCheckLogsTable->newEntity([
            'id' => $domainEntity->id()->toString(),
            'mail_id' => $domainEntity->mailId()->toInt(),
            'original_message_id' => null,
            'checked_address' => '',
            'checked_at' => $domainEntity->checkedAt()->format('Y-m-d\TH:i:s'),
            'created' => $domainEntity->created()->format('Y-m-d\TH:i:s'),
            'created_by' => $domainEntity->createdBy()->toIntOrNull(),
            'created_ip' => $domainEntity->createdIp()->toStringOrNull(),
        ], [
            'validate' => false,
        ]);
    }

    /**
     * @param \App\Model\Entity\Mail\MailReceivedCheckLog $ormEntity
     * @return \App\Domain\Mail\Entity\MailReceivedCheckLog
     */
    public function toDomainReceivedCheckLogEntity(OrmReceivedCheckLogEntity $ormEntity): DomainReceivedCheckLogEntity
    {
        return new DomainReceivedCheckLogEntity(
            id: StrictCast::toString($ormEntity->id),
            mail_id: StrictCast::toString($ormEntity->mail_id),
            checked_at: StrictCast::toString($ormEntity->checked_at->format('Y-m-d\TH:i:s')),
            created: StrictCast::toString($ormEntity->created->format('Y-m-d\TH:i:s')),
            created_by: Cast::toStringOrNull($ormEntity->created_by),
            created_ip: Cast::toStringOrNull($ormEntity->created_ip),
        );
    }

    /**
     * @param \App\Domain\Mail\Entity\MailBounceLog $domainEntity
     * @return \App\Model\Entity\Mail\MailBounceLog
     */
    public function toNewOrmBounceLogEntity(DomainBounceLogEntity $domainEntity): OrmBounceLogEntity
    {
        /** @var \App\Model\Entity\Mail\MailBounceLog */
        return $this->bounceLogsTable->newEntity([
            'id' => $domainEntity->id()->toString(),
            'mail_id' => $domainEntity->mailId()->toInt(),
            'bounced_email' => $domainEntity->bouncedAddress()->toString(),
            'diagnostic_code' => $domainEntity->bounceReason()->toStringOrNull(),
            'bounce_type' => 'UNKNOWN',
            'bounced_at' => $domainEntity->bouncedAt()->format('Y-m-d\TH:i:s'),
            'created' => $domainEntity->created()->format('Y-m-d\TH:i:s'),
            'created_by' => $domainEntity->createdBy()->toIntOrNull(),
            'created_ip' => $domainEntity->createdIp()->toStringOrNull(),
        ], [
            'validate' => false,
        ]);
    }

    /**
     * @param \App\Model\Entity\Mail\MailBounceLog $ormEntity
     * @return \App\Domain\Mail\Entity\MailBounceLog
     */
    public function toDomainBounceLogEntity(OrmBounceLogEntity $ormEntity): DomainBounceLogEntity
    {
        return new DomainBounceLogEntity(
            id: StrictCast::toString($ormEntity->id),
            mail_id: StrictCast::toString($ormEntity->mail_id),
            bounced_address: StrictCast::toString($ormEntity->bounced_email ?? $ormEntity->bounced_address),
            bounce_reason: Cast::toStringOrNull($ormEntity->diagnostic_code ?? $ormEntity->bounce_reason),
            bounced_at: StrictCast::toString($ormEntity->bounced_at->format('Y-m-d\TH:i:s')),
            created: StrictCast::toString($ormEntity->created->format('Y-m-d\TH:i:s')),
            created_by: Cast::toStringOrNull($ormEntity->created_by),
            created_ip: Cast::toStringOrNull($ormEntity->created_ip),
        );
    }
}
