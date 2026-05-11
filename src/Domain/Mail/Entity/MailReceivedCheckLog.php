<?php
declare(strict_types=1);

namespace App\Domain\Mail\Entity;

use App\Domain\Mail\ValueObject\MailReceivedCheckLogs as Vo;
use App\Domain\Shared\ValueObject as SVo;

final class MailReceivedCheckLog
{
    /**
     * @param ?string $id
     * @param ?string $mail_id
     * @param ?string $original_message_id
     * @param ?string $checked_address
     * @param ?string $checked_at
     * @param ?string $created
     * @param ?string $created_by
     * @param ?string $created_ip
     */
    public function __construct(
        private readonly ?string $id,
        private readonly ?string $mail_id,
        private readonly ?string $original_message_id,
        private readonly ?string $checked_address,
        private readonly ?string $checked_at,
        private readonly ?string $created,
        private readonly ?string $created_by,
        private readonly ?string $created_ip,
    ) {
    }

    /**
     * @return \App\Domain\Mail\ValueObject\MailReceivedCheckLogs\Id
     */
    public function id(): Vo\Id
    {
        return Vo\Id::fromString($this->id);
    }

    /**
     * @return \App\Domain\Mail\ValueObject\MailReceivedCheckLogs\MailId
     */
    public function mailId(): Vo\MailId
    {
        return new Vo\MailId($this->mail_id);
    }

    /**
     * @return \App\Domain\Mail\ValueObject\MailReceivedCheckLogs\OriginalMessageId
     */
    public function originalMessageId(): Vo\OriginalMessageId
    {
        return Vo\OriginalMessageId::fromString($this->original_message_id);
    }

    /**
     * @return \App\Domain\Mail\ValueObject\MailReceivedCheckLogs\CheckedAddress
     */
    public function checkedAddress(): Vo\CheckedAddress
    {
        return Vo\CheckedAddress::fromString($this->checked_address);
    }

    /**
     * @return \App\Domain\Mail\ValueObject\MailReceivedCheckLogs\CheckedAt
     */
    public function checkedAt(): Vo\CheckedAt
    {
        return new Vo\CheckedAt($this->checked_at);
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
