<?php
declare(strict_types=1);

namespace App\Test\TestCase\Domain\Mail;

use App\Domain\Mail\Entity\Mail;
use App\Domain\Mail\Entity\MailBounceLog;
use App\Domain\Mail\Entity\MailReceivedCheckLog;
use App\Domain\Mail\Entity\MailSentLog;
use App\Domain\Mail\ValueObject as Vo;
use App\Domain\Mail\ValueObject\MailBounceLogs as BounceLogVo;
use App\Domain\Mail\ValueObject\MailReceivedCheckLogs as ReceivedCheckLogVo;
use App\Domain\Mail\ValueObject\MailSentLogs as SentLogVo;
use App\Domain\Shared\ValueObject as SVo;
use Cake\TestSuite\TestCase;

final class MailDomainSchemaAlignmentTest extends TestCase
{
    public function testMailEntityOriginalMessageIdAccessorReturnsValueObject(): void
    {
        $entity = new Mail(
            id: new Vo\Id('1'),
            original_message_id: Vo\OriginalMessageId::fromString('<m-1@example.com>'),
            related_data_key: Vo\RelatedDataKey::fromString('rel-1'),
            send_status: Vo\SendStatus::fromString('WAITING'),
            send_scheduled_at: new Vo\SendScheduledAt('2026-05-11T00:00:00'),
            title: Vo\Title::fromString('subject'),
            body: Vo\Body::fromString('body'),
            mail_to: Vo\MailTo::fromString('to@example.com'),
            mail_cc: Vo\MailCc::fromString(null),
            mail_bcc: Vo\MailBcc::fromString(null),
            mail_received_check: Vo\MailReceivedCheck::fromString('received@example.com'),
            mail_return_path: Vo\MailReturnPath::fromString('bounce@example.com'),
            created: new SVo\Created('2026-05-11T00:00:00'),
            created_by: new SVo\CreatedBy('1'),
            created_ip: new SVo\CreatedIp('127.0.0.1'),
            modified: new SVo\Modified('2026-05-11T00:00:00'),
            modified_by: new SVo\ModifiedBy('1'),
            modified_ip: new SVo\ModifiedIp('127.0.0.1'),
        );

        $this->assertSame('<m-1@example.com>', $entity->originalMessageId()->toStringOrNull());
    }

    public function testMailSentLogSupportsOriginalMessageId(): void
    {
        $entity = new MailSentLog(
            id: SentLogVo\Id::fromString('11111111-1111-1111-1111-111111111111'),
            mail_id: new SentLogVo\MailId('1'),
            original_message_id: SentLogVo\OriginalMessageId::fromString('<m-1@example.com>'),
            send_status: SentLogVo\SendStatus::fromString('SENT'),
            error_message: SentLogVo\ErrorMessage::fromString(null),
            sent_at: new SentLogVo\SentAt('2026-05-11T00:00:00'),
            created: new SVo\Created('2026-05-11T00:00:00'),
            created_by: new SVo\CreatedBy('1'),
            created_ip: new SVo\CreatedIp('127.0.0.1'),
        );

        $this->assertSame('<m-1@example.com>', $entity->originalMessageId()->toStringOrNull());
    }

    public function testMailReceivedCheckLogSupportsNewColumns(): void
    {
        $entity = new MailReceivedCheckLog(
            id: ReceivedCheckLogVo\Id::fromString('22222222-2222-2222-2222-222222222222'),
            mail_id: new ReceivedCheckLogVo\MailId('1'),
            original_message_id: ReceivedCheckLogVo\OriginalMessageId::fromString('<m-2@example.com>'),
            checked_address: ReceivedCheckLogVo\CheckedAddress::fromString('received@example.com'),
            checked_at: new ReceivedCheckLogVo\CheckedAt('2026-05-11T00:00:00'),
            created: new SVo\Created('2026-05-11T00:00:00'),
            created_by: new SVo\CreatedBy('1'),
            created_ip: new SVo\CreatedIp('127.0.0.1'),
        );

        $this->assertSame('<m-2@example.com>', $entity->originalMessageId()->toStringOrNull());
        $this->assertSame('received@example.com', $entity->checkedAddress()->toString());
    }

    public function testMailBounceLogSupportsNewSchemaColumns(): void
    {
        $entity = new MailBounceLog(
            id: BounceLogVo\Id::fromString('33333333-3333-3333-3333-333333333333'),
            mail_id: new BounceLogVo\MailId('1'),
            original_message_id: BounceLogVo\OriginalMessageId::fromString('<m-3@example.com>'),
            bounced_email: BounceLogVo\BouncedEmail::fromString('bounce@example.com'),
            recipient_type: BounceLogVo\RecipientType::fromString('TO'),
            action: BounceLogVo\Action::fromString('failed'),
            status_code: BounceLogVo\StatusCode::fromString('5.1.1'),
            diagnostic_code: BounceLogVo\DiagnosticCode::fromString('Mailbox not found'),
            bounce_type: BounceLogVo\BounceType::fromString('HARD'),
            remote_mta: BounceLogVo\RemoteMta::fromString('mx.example.com'),
            reporting_mta: BounceLogVo\ReportingMta::fromString('report.example.com'),
            arrival_date: new BounceLogVo\ArrivalDate('2026-05-11T00:00:00'),
            bounced_at: new BounceLogVo\BouncedAt('2026-05-11T00:00:00'),
            raw_headers: BounceLogVo\RawHeaders::fromString('header'),
            raw_body: BounceLogVo\RawBody::fromString('body'),
            raw_message: BounceLogVo\RawMessage::fromString('message'),
            parsed_json: BounceLogVo\ParsedJson::fromString('{"k":"v"}'),
            provider: BounceLogVo\Provider::fromString('gmail'),
            is_auto_generated: BounceLogVo\IsAutoGenerated::fromString('1'),
            created: new SVo\Created('2026-05-11T00:00:00'),
            created_by: new SVo\CreatedBy('1'),
            created_ip: new SVo\CreatedIp('127.0.0.1'),
        );

        $this->assertSame('bounce@example.com', $entity->bouncedEmail()->toString());
        $this->assertSame('Mailbox not found', $entity->diagnosticCode()->toStringOrNull());
        $this->assertSame('HARD', $entity->bounceType()->toString());
        $this->assertSame('{"k":"v"}', $entity->parsedJson()->toString());
        $this->assertSame(1, $entity->isAutoGenerated()->toIntOrNull());
    }
}
