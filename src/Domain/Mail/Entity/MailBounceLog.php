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
     * @param ?string $bounced_address
     * @param ?string $bounce_reason
     * @param ?string $bounced_at
     * @param ?string $created
     * @param ?string $created_by
     * @param ?string $created_ip
     */
    public function __construct(
        private readonly ?string $id,
        private readonly ?string $mail_id,
        private readonly ?string $bounced_address,
        private readonly ?string $bounce_reason,
        private readonly ?string $bounced_at,
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
     * @return \App\Domain\Mail\ValueObject\MailBounceLogs\BouncedAddress
     */
    public function bouncedAddress(): Vo\BouncedAddress
    {
        return Vo\BouncedAddress::fromString($this->bounced_address);
    }

    /**
     * @return \App\Domain\Mail\ValueObject\MailBounceLogs\BounceReason
     */
    public function bounceReason(): Vo\BounceReason
    {
        return Vo\BounceReason::fromString($this->bounce_reason);
    }

    /**
     * @return \App\Domain\Mail\ValueObject\MailBounceLogs\BouncedAt
     */
    public function bouncedAt(): Vo\BouncedAt
    {
        return new Vo\BouncedAt($this->bounced_at);
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
