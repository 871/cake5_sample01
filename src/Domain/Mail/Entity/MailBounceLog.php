<?php
declare(strict_types=1);

namespace App\Domain\Mail\Entity;

use App\Domain\Mail\ValueObject\MailBounceLogs as Vo;
use App\Domain\Shared\ValueObject as SVo;

final class MailBounceLog
{
    /**
     * @param ?string $id
     * @param ?string $mail_id
     * @param ?string $original_message_id
     * @param ?string $bounced_email
     * @param ?string $recipient_type
     * @param ?string $action
     * @param ?string $status_code
     * @param ?string $diagnostic_code
     * @param ?string $bounce_type
     * @param ?string $remote_mta
     * @param ?string $reporting_mta
     * @param ?string $arrival_date
     * @param ?string $bounced_at
     * @param ?string $raw_headers
     * @param ?string $raw_body
     * @param ?string $raw_message
     * @param ?string $parsed_json
     * @param ?string $provider
     * @param ?string $is_auto_generated
     * @param ?string $created
     * @param ?string $created_by
     * @param ?string $created_ip
     */
    public function __construct(
        private readonly ?string $id,
        private readonly ?string $mail_id,
        private readonly ?string $original_message_id,
        private readonly ?string $bounced_email,
        private readonly ?string $recipient_type,
        private readonly ?string $action,
        private readonly ?string $status_code,
        private readonly ?string $diagnostic_code,
        private readonly ?string $bounce_type,
        private readonly ?string $remote_mta,
        private readonly ?string $reporting_mta,
        private readonly ?string $arrival_date,
        private readonly ?string $bounced_at,
        private readonly ?string $raw_headers,
        private readonly ?string $raw_body,
        private readonly ?string $raw_message,
        private readonly ?string $parsed_json,
        private readonly ?string $provider,
        private readonly ?string $is_auto_generated,
        private readonly ?string $created,
        private readonly ?string $created_by,
        private readonly ?string $created_ip,
    ) {
    }

    /**
     * @return \App\Domain\Mail\ValueObject\MailBounceLogs\Id
     */
    public function id(): Vo\Id
    {
        return Vo\Id::fromString($this->id);
    }

    /**
     * @return \App\Domain\Mail\ValueObject\MailBounceLogs\MailId
     */
    public function mailId(): Vo\MailId
    {
        return new Vo\MailId($this->mail_id);
    }

    /**
     * @return \App\Domain\Mail\ValueObject\MailBounceLogs\OriginalMessageId
     */
    public function originalMessageId(): Vo\OriginalMessageId
    {
        return Vo\OriginalMessageId::fromString($this->original_message_id);
    }

    /**
     * @return \App\Domain\Mail\ValueObject\MailBounceLogs\BouncedEmail
     */
    public function bouncedEmail(): Vo\BouncedEmail
    {
        return Vo\BouncedEmail::fromString($this->bounced_email);
    }

    /**
     * @return \App\Domain\Mail\ValueObject\MailBounceLogs\RecipientType
     */
    public function recipientType(): Vo\RecipientType
    {
        return Vo\RecipientType::fromString($this->recipient_type);
    }

    /**
     * @return \App\Domain\Mail\ValueObject\MailBounceLogs\Action
     */
    public function action(): Vo\Action
    {
        return Vo\Action::fromString($this->action);
    }

    /**
     * @return \App\Domain\Mail\ValueObject\MailBounceLogs\StatusCode
     */
    public function statusCode(): Vo\StatusCode
    {
        return Vo\StatusCode::fromString($this->status_code);
    }

    /**
     * @return \App\Domain\Mail\ValueObject\MailBounceLogs\DiagnosticCode
     */
    public function diagnosticCode(): Vo\DiagnosticCode
    {
        return Vo\DiagnosticCode::fromString($this->diagnostic_code);
    }

    /**
     * @return \App\Domain\Mail\ValueObject\MailBounceLogs\BounceType
     */
    public function bounceType(): Vo\BounceType
    {
        return Vo\BounceType::fromString($this->bounce_type);
    }

    /**
     * @return \App\Domain\Mail\ValueObject\MailBounceLogs\RemoteMta
     */
    public function remoteMta(): Vo\RemoteMta
    {
        return Vo\RemoteMta::fromString($this->remote_mta);
    }

    /**
     * @return \App\Domain\Mail\ValueObject\MailBounceLogs\ReportingMta
     */
    public function reportingMta(): Vo\ReportingMta
    {
        return Vo\ReportingMta::fromString($this->reporting_mta);
    }

    /**
     * @return \App\Domain\Mail\ValueObject\MailBounceLogs\ArrivalDate
     */
    public function arrivalDate(): Vo\ArrivalDate
    {
        return new Vo\ArrivalDate($this->arrival_date);
    }

    /**
     * @return \App\Domain\Mail\ValueObject\MailBounceLogs\BouncedAt
     */
    public function bouncedAt(): Vo\BouncedAt
    {
        return new Vo\BouncedAt($this->bounced_at);
    }

    /**
     * @return \App\Domain\Mail\ValueObject\MailBounceLogs\RawHeaders
     */
    public function rawHeaders(): Vo\RawHeaders
    {
        return Vo\RawHeaders::fromString($this->raw_headers);
    }

    /**
     * @return \App\Domain\Mail\ValueObject\MailBounceLogs\RawBody
     */
    public function rawBody(): Vo\RawBody
    {
        return Vo\RawBody::fromString($this->raw_body);
    }

    /**
     * @return \App\Domain\Mail\ValueObject\MailBounceLogs\RawMessage
     */
    public function rawMessage(): Vo\RawMessage
    {
        return Vo\RawMessage::fromString($this->raw_message);
    }

    /**
     * @return \App\Domain\Mail\ValueObject\MailBounceLogs\ParsedJson
     */
    public function parsedJson(): Vo\ParsedJson
    {
        return Vo\ParsedJson::fromString($this->parsed_json);
    }

    /**
     * @return \App\Domain\Mail\ValueObject\MailBounceLogs\Provider
     */
    public function provider(): Vo\Provider
    {
        return Vo\Provider::fromString($this->provider);
    }

    /**
     * @return \App\Domain\Mail\ValueObject\MailBounceLogs\IsAutoGenerated
     */
    public function isAutoGenerated(): Vo\IsAutoGenerated
    {
        return Vo\IsAutoGenerated::fromString($this->is_auto_generated);
    }

    /**
     * @return \App\Domain\Shared\ValueObject\Created
     */
    public function created(): SVo\Created
    {
        return new SVo\Created($this->created);
    }

    /**
     * @return \App\Domain\Shared\ValueObject\CreatedBy
     */
    public function createdBy(): SVo\CreatedBy
    {
        return new SVo\CreatedBy($this->created_by);
    }

    /**
     * @return \App\Domain\Shared\ValueObject\CreatedIp
     */
    public function createdIp(): SVo\CreatedIp
    {
        return new SVo\CreatedIp($this->created_ip);
    }
}
