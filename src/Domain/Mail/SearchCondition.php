<?php
declare(strict_types=1);

namespace App\Domain\Mail;

use App\Domain\Mail\ValueObject\SendStatus;

class SearchCondition
{
    public const DEFAULT_LIMIT = 100;

    /**
     * @param \App\Domain\Mail\ValueObject\SendScheduledAt $sendScheduledAtFrom
     * @param \App\Domain\Mail\ValueObject\SendScheduledAt $sendScheduledAtTo
     * @param \App\Domain\Mail\ValueObject\SendStatus|null $sendStatus
     * @param \App\Domain\Mail\ValueObject\RelatedDataKey $relatedDataKey
     * @param int $limit
     */
    public function __construct(
        private readonly ValueObject\SendScheduledAt $sendScheduledAtFrom,
        private readonly ValueObject\SendScheduledAt $sendScheduledAtTo,
        private readonly ?ValueObject\SendStatus $sendStatus,
        private readonly ValueObject\RelatedDataKey $relatedDataKey,
        private readonly int $limit = self::DEFAULT_LIMIT,
    ) {
        // 処理なし
    }

    /**
     * @return \App\Domain\Mail\ValueObject\SendScheduledAt
     */
    public function getSendScheduledAtFrom(): ValueObject\SendScheduledAt
    {
        return $this->sendScheduledAtFrom;
    }

    /**
     * @return \App\Domain\Mail\ValueObject\SendScheduledAt
     */
    public function getSendScheduledAtTo(): ValueObject\SendScheduledAt
    {
        return $this->sendScheduledAtTo;
    }

    /**
     * @return \App\Domain\Mail\ValueObject\SendStatus|null
     */
    public function getSendStatus(): ?ValueObject\SendStatus
    {
        return $this->sendStatus;
    }

    /**
     * @return \App\Domain\Mail\ValueObject\RelatedDataKey
     */
    public function getRelatedDataKey(): ValueObject\RelatedDataKey
    {
        return $this->relatedDataKey;
    }

    /**
     * @return int
     */
    public function getLimit(): int
    {
        return $this->limit;
    }
}
