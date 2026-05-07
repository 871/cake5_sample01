<?php
declare(strict_types=1);

namespace App\Domain\Mail\Entity;

use App\Domain\Mail\ValueObject as Vo;
use App\Domain\Shared\ValueObject as SVo;

final class Mail
{
    /**
     * @param ?string $id
     * @param ?string $related_data_key
     * @param ?string $send_status
     * @param ?string $send_scheduled_at
     * @param ?string $title
     * @param ?string $body
     * @param ?string $mail_to
     * @param ?string $mail_cc
     * @param ?string $mail_bcc
     * @param ?string $mail_received_check
     * @param ?string $mail_return_path
     * @param ?string $created
     * @param ?string $created_by
     * @param ?string $created_ip
     * @param ?string $modified
     * @param ?string $modified_by
     * @param ?string $modified_ip
     * @param array<\App\Domain\Mail\Entity\MailSentLog> $mail_sent_logs
     * @param array<\App\Domain\Mail\Entity\MailReceivedCheckLog> $mail_received_check_logs
     * @param array<\App\Domain\Mail\Entity\MailBounceLog> $mail_bounce_logs
     */
    public function __construct(
        private readonly ?string $id,
        private readonly ?string $related_data_key,
        private readonly ?string $send_status,
        private readonly ?string $send_scheduled_at,
        private readonly ?string $title,
        private readonly ?string $body,
        private readonly ?string $mail_to,
        private readonly ?string $mail_cc,
        private readonly ?string $mail_bcc,
        private readonly ?string $mail_received_check,
        private readonly ?string $mail_return_path,
        private readonly ?string $created,
        private readonly ?string $created_by,
        private readonly ?string $created_ip,
        private readonly ?string $modified,
        private readonly ?string $modified_by,
        private readonly ?string $modified_ip,
        private readonly array $mail_sent_logs = [],
        private readonly array $mail_received_check_logs = [],
        private readonly array $mail_bounce_logs = [],
    ) {
    }

    /**
     * @return \App\Domain\Mail\ValueObject\Id
     */
    public function id(): Vo\Id
    {
        return new Vo\Id($this->id);
    }

    /**
     * @return \App\Domain\Mail\ValueObject\RelatedDataKey
     */
    public function relatedDataKey(): Vo\RelatedDataKey
    {
        return Vo\RelatedDataKey::fromString($this->related_data_key);
    }

    /**
     * @return \App\Domain\Mail\ValueObject\SendStatus
     */
    public function sendStatus(): Vo\SendStatus
    {
        return Vo\SendStatus::fromString($this->send_status ?? '');
    }

    /**
     * @return \App\Domain\Mail\ValueObject\SendScheduledAt
     */
    public function sendScheduledAt(): Vo\SendScheduledAt
    {
        return new Vo\SendScheduledAt($this->send_scheduled_at);
    }

    /**
     * @return \App\Domain\Mail\ValueObject\Title
     */
    public function title(): Vo\Title
    {
        return Vo\Title::fromString($this->title);
    }

    /**
     * @return \App\Domain\Mail\ValueObject\Body
     */
    public function body(): Vo\Body
    {
        return Vo\Body::fromString($this->body);
    }

    /**
     * @return \App\Domain\Mail\ValueObject\MailTo
     */
    public function mailTo(): Vo\MailTo
    {
        return Vo\MailTo::fromString($this->mail_to);
    }

    /**
     * @return \App\Domain\Mail\ValueObject\MailCc
     */
    public function mailCc(): Vo\MailCc
    {
        return Vo\MailCc::fromString($this->mail_cc);
    }

    /**
     * @return \App\Domain\Mail\ValueObject\MailBcc
     */
    public function mailBcc(): Vo\MailBcc
    {
        return Vo\MailBcc::fromString($this->mail_bcc);
    }

    /**
     * @return \App\Domain\Mail\ValueObject\MailReceivedCheck
     */
    public function mailReceivedCheck(): Vo\MailReceivedCheck
    {
        return Vo\MailReceivedCheck::fromString($this->mail_received_check);
    }

    /**
     * @return \App\Domain\Mail\ValueObject\MailReturnPath
     */
    public function mailReturnPath(): Vo\MailReturnPath
    {
        return Vo\MailReturnPath::fromString($this->mail_return_path);
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

    /**
     * @return \App\Domain\Shared\ValueObject\Modified
     */
    public function modified(): SVo\Modified
    {
        return new SVo\Modified($this->modified);
    }

    /**
     * @return \App\Domain\Shared\ValueObject\ModifiedBy
     */
    public function modifiedBy(): SVo\ModifiedBy
    {
        return new SVo\ModifiedBy($this->modified_by);
    }

    /**
     * @return \App\Domain\Shared\ValueObject\ModifiedIp
     */
    public function modifiedIp(): SVo\ModifiedIp
    {
        return new SVo\ModifiedIp($this->modified_ip);
    }

    /**
     * @return array<\App\Domain\Mail\Entity\MailSentLog>
     */
    public function mailSentLogs(): array
    {
        return $this->mail_sent_logs;
    }

    /**
     * @return array<\App\Domain\Mail\Entity\MailReceivedCheckLog>
     */
    public function mailReceivedCheckLogs(): array
    {
        return $this->mail_received_check_logs;
    }

    /**
     * @return array<\App\Domain\Mail\Entity\MailBounceLog>
     */
    public function mailBounceLogs(): array
    {
        return $this->mail_bounce_logs;
    }
}
