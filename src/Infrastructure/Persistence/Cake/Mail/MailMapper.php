<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Cake\Mail;

use App\Domain\Mail\Entity\Mail as DomainEntity;
use App\Domain\Mail\Entity\MailBounceLog as DomainBounceLogEntity;
use App\Domain\Mail\Entity\MailReceivedCheckLog as DomainReceivedCheckLogEntity;
use App\Domain\Mail\Entity\MailSentLog as DomainSentLogEntity;
use App\Domain\Mail\ValueObject as Vo;
use App\Domain\Mail\ValueObject\MailBounceLogs as BounceLogVo;
use App\Domain\Mail\ValueObject\MailReceivedCheckLogs as ReceivedCheckLogVo;
use App\Domain\Mail\ValueObject\MailSentLogs as SentLogVo;
use App\Domain\Shared\ValueObject as SVo;
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
use JsonException;
use RuntimeException;

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
            'original_message_id' => $domainEntity->originalMessageId()->toStringOrNull(),
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
            id: new Vo\Id(StrictCast::toString($ormEntity->id)),
            original_message_id: Vo\OriginalMessageId::fromString(
                Cast::toStringOrNull($ormEntity->original_message_id),
            ),
            related_data_key: Vo\RelatedDataKey::fromString(
                StrictCast::toString($ormEntity->related_data_key),
            ),
            send_status: Vo\SendStatus::fromString(StrictCast::toString($ormEntity->send_status)),
            send_scheduled_at: new Vo\SendScheduledAt(
                StrictCast::toString($ormEntity->send_scheduled_at->format('Y-m-d\TH:i:s')),
            ),
            title: Vo\Title::fromString(StrictCast::toString($ormEntity->title)),
            body: Vo\Body::fromString(StrictCast::toString($ormEntity->body)),
            mail_to: Vo\MailTo::fromString(StrictCast::toString($ormEntity->mail_to)),
            mail_cc: Vo\MailCc::fromString(Cast::toStringOrNull($ormEntity->mail_cc)),
            mail_bcc: Vo\MailBcc::fromString(Cast::toStringOrNull($ormEntity->mail_bcc)),
            mail_received_check: Vo\MailReceivedCheck::fromString(
                StrictCast::toString($ormEntity->mail_received_check),
            ),
            mail_return_path: Vo\MailReturnPath::fromString(
                StrictCast::toString($ormEntity->mail_return_path),
            ),
            created: new SVo\Created(StrictCast::toString($ormEntity->created->format('Y-m-d\TH:i:s'))),
            created_by: new SVo\CreatedBy(Cast::toStringOrNull($ormEntity->created_by)),
            created_ip: new SVo\CreatedIp(Cast::toStringOrNull($ormEntity->created_ip)),
            modified: new SVo\Modified(StrictCast::toString($ormEntity->modified->format('Y-m-d\TH:i:s'))),
            modified_by: new SVo\ModifiedBy(Cast::toStringOrNull($ormEntity->modified_by)),
            modified_ip: new SVo\ModifiedIp(Cast::toStringOrNull($ormEntity->modified_ip)),
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
            'original_message_id' => $domainEntity->originalMessageId()->toStringOrNull(),
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
            id: SentLogVo\Id::fromString(StrictCast::toString($ormEntity->id)),
            mail_id: new SentLogVo\MailId(StrictCast::toString($ormEntity->mail_id)),
            original_message_id: SentLogVo\OriginalMessageId::fromString(
                Cast::toStringOrNull($ormEntity->original_message_id),
            ),
            send_status: SentLogVo\SendStatus::fromString(StrictCast::toString($ormEntity->send_status)),
            error_message: SentLogVo\ErrorMessage::fromString(Cast::toStringOrNull($ormEntity->error_message)),
            sent_at: new SentLogVo\SentAt(StrictCast::toString($ormEntity->sent_at->format('Y-m-d\TH:i:s'))),
            created: new SVo\Created(StrictCast::toString($ormEntity->created->format('Y-m-d\TH:i:s'))),
            created_by: new SVo\CreatedBy(Cast::toStringOrNull($ormEntity->created_by)),
            created_ip: new SVo\CreatedIp(Cast::toStringOrNull($ormEntity->created_ip)),
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
            'original_message_id' => $domainEntity->originalMessageId()->toStringOrNull(),
            'checked_address' => $domainEntity->checkedAddress()->toString(),
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
            id: ReceivedCheckLogVo\Id::fromString(StrictCast::toString($ormEntity->id)),
            mail_id: new ReceivedCheckLogVo\MailId(StrictCast::toString($ormEntity->mail_id)),
            original_message_id: ReceivedCheckLogVo\OriginalMessageId::fromString(
                Cast::toStringOrNull($ormEntity->original_message_id),
            ),
            checked_address: ReceivedCheckLogVo\CheckedAddress::fromString(
                StrictCast::toString($ormEntity->checked_address),
            ),
            checked_at: new ReceivedCheckLogVo\CheckedAt(
                StrictCast::toString($ormEntity->checked_at->format('Y-m-d\TH:i:s')),
            ),
            created: new SVo\Created(StrictCast::toString($ormEntity->created->format('Y-m-d\TH:i:s'))),
            created_by: new SVo\CreatedBy(Cast::toStringOrNull($ormEntity->created_by)),
            created_ip: new SVo\CreatedIp(Cast::toStringOrNull($ormEntity->created_ip)),
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
            'original_message_id' => $domainEntity->originalMessageId()->toStringOrNull(),
            'bounced_email' => $domainEntity->bouncedEmail()->toString(),
            'recipient_type' => $domainEntity->recipientType()->toStringOrNull(),
            'action' => $domainEntity->action()->toStringOrNull(),
            'status_code' => $domainEntity->statusCode()->toStringOrNull(),
            'diagnostic_code' => $domainEntity->diagnosticCode()->toStringOrNull(),
            'bounce_type' => $domainEntity->bounceType()->toString(),
            'remote_mta' => $domainEntity->remoteMta()->toStringOrNull(),
            'reporting_mta' => $domainEntity->reportingMta()->toStringOrNull(),
            'arrival_date' => $domainEntity->arrivalDate()->toDateTimeOrNull()?->format('Y-m-d\TH:i:s'),
            'bounced_at' => $domainEntity->bouncedAt()->format('Y-m-d\TH:i:s'),
            'raw_headers' => $domainEntity->rawHeaders()->toStringOrNull(),
            'raw_body' => $domainEntity->rawBody()->toStringOrNull(),
            'raw_message' => $domainEntity->rawMessage()->toStringOrNull(),
            'parsed_json' => $domainEntity->parsedJson()->toStringOrNull(),
            'provider' => $domainEntity->provider()->toStringOrNull(),
            'is_auto_generated' => $domainEntity->isAutoGenerated()->toIntOrNull(),
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
            id: BounceLogVo\Id::fromString(StrictCast::toString($ormEntity->id)),
            mail_id: new BounceLogVo\MailId(StrictCast::toString($ormEntity->mail_id)),
            original_message_id: BounceLogVo\OriginalMessageId::fromString(
                Cast::toStringOrNull($ormEntity->original_message_id),
            ),
            bounced_email: BounceLogVo\BouncedEmail::fromString(StrictCast::toString($ormEntity->bounced_email)),
            recipient_type: BounceLogVo\RecipientType::fromString(Cast::toStringOrNull($ormEntity->recipient_type)),
            action: BounceLogVo\Action::fromString(Cast::toStringOrNull($ormEntity->action)),
            status_code: BounceLogVo\StatusCode::fromString(Cast::toStringOrNull($ormEntity->status_code)),
            diagnostic_code: BounceLogVo\DiagnosticCode::fromString(
                Cast::toStringOrNull($ormEntity->diagnostic_code),
            ),
            bounce_type: BounceLogVo\BounceType::fromString(StrictCast::toString($ormEntity->bounce_type)),
            remote_mta: BounceLogVo\RemoteMta::fromString(Cast::toStringOrNull($ormEntity->remote_mta)),
            reporting_mta: BounceLogVo\ReportingMta::fromString(Cast::toStringOrNull($ormEntity->reporting_mta)),
            arrival_date: new BounceLogVo\ArrivalDate(
                Cast::toStringOrNull($ormEntity->arrival_date?->format('Y-m-d\TH:i:s')),
            ),
            bounced_at: new BounceLogVo\BouncedAt(StrictCast::toString($ormEntity->bounced_at->format('Y-m-d\TH:i:s'))),
            raw_headers: BounceLogVo\RawHeaders::fromString(Cast::toStringOrNull($ormEntity->raw_headers)),
            raw_body: BounceLogVo\RawBody::fromString(Cast::toStringOrNull($ormEntity->raw_body)),
            raw_message: BounceLogVo\RawMessage::fromString(Cast::toStringOrNull($ormEntity->raw_message)),
            parsed_json: BounceLogVo\ParsedJson::fromString(
                $this->toJsonStringOrNull($ormEntity->parsed_json),
            ),
            provider: BounceLogVo\Provider::fromString(Cast::toStringOrNull($ormEntity->provider)),
            is_auto_generated: BounceLogVo\IsAutoGenerated::fromString(
                Cast::toStringOrNull($ormEntity->is_auto_generated),
            ),
            created: new SVo\Created(StrictCast::toString($ormEntity->created->format('Y-m-d\TH:i:s'))),
            created_by: new SVo\CreatedBy(Cast::toStringOrNull($ormEntity->created_by)),
            created_ip: new SVo\CreatedIp(Cast::toStringOrNull($ormEntity->created_ip)),
        );
    }

    /**
     * @param mixed $value
     * @return ?string
     */
    private function toJsonStringOrNull(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }
        if (is_string($value)) {
            return $value;
        }
        if (is_array($value)) {
            try {
                return json_encode($value, JSON_THROW_ON_ERROR);
            } catch (JsonException $e) {
                throw new RuntimeException('MailMapper parsed_json encode failed: ' . $e->getMessage(), 0, $e);
            }
        }

        return Cast::toStringOrNull($value);
    }
}
