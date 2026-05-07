<?php
declare(strict_types=1);

namespace App\Domain\Mail\Entity;

use App\Domain\Mail\ValueObject\MailSentLogs as Vo;
use App\Domain\Shared\ValueObject as SVo;

final class MailSentLog
{
    /**
     * @param ?string $id
     * @param ?string $mail_id
     * @param ?string $send_status
     * @param ?string $error_message
     * @param ?string $sent_at
     * @param ?string $created
     * @param ?string $created_by
     * @param ?string $created_ip
     */
    public function __construct(
        private readonly ?string $id,
        private readonly ?string $mail_id,
        private readonly ?string $send_status,
        private readonly ?string $error_message,
        private readonly ?string $sent_at,
        private readonly ?string $created,
        private readonly ?string $created_by,
        private readonly ?string $created_ip,
    ) {
    }

    /**
     * @return \App\Domain\Mail\ValueObject\MailSentLogs\Id
     */
    public function id(): Vo\Id
    {
        return Vo\Id::fromString($this->id);
    }

    /**
     * @return \App\Domain\Mail\ValueObject\MailSentLogs\MailId
     */
    public function mailId(): Vo\MailId
    {
        return new Vo\MailId($this->mail_id);
    }

    /**
     * @return \App\Domain\Mail\ValueObject\MailSentLogs\SendStatus
     */
    public function sendStatus(): Vo\SendStatus
    {
        return Vo\SendStatus::fromString($this->send_status ?? '');
    }

    /**
     * @return \App\Domain\Mail\ValueObject\MailSentLogs\ErrorMessage
     */
    public function errorMessage(): Vo\ErrorMessage
    {
        return Vo\ErrorMessage::fromString($this->error_message);
    }

    /**
     * @return \App\Domain\Mail\ValueObject\MailSentLogs\SentAt
     */
    public function sentAt(): Vo\SentAt
    {
        return new Vo\SentAt($this->sent_at);
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
