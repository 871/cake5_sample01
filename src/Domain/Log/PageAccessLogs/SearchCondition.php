<?php
declare(strict_types=1);

namespace App\Domain\Log\PageAccessLogs;

class SearchCondition
{
    public const DEFAULT_LIMIT = 100;

    /**
     * @param \App\Domain\Log\PageAccessLogs\ValueObject\Accessed $accessedFrom
     * @param \App\Domain\Log\PageAccessLogs\ValueObject\Accessed $accessedTo
     * @param \App\Domain\Log\PageAccessLogs\ValueObject\AccountType $accountType
     * @param \App\Domain\Log\PageAccessLogs\ValueObject\AccountId $accountId
     * @param \App\Domain\Log\PageAccessLogs\ValueObject\Search\Keyword $keyword
     * @param \App\Domain\Log\PageAccessLogs\ValueObject\NavigationType $navigationType
     * @param \App\Domain\Log\PageAccessLogs\ValueObject\Id $cursorId
     * @param \App\Domain\Log\PageAccessLogs\ValueObject\Accessed $cursorAccessed
     * @param int $limit
     */
    public function __construct(
        private readonly ValueObject\Accessed $accessedFrom,
        private readonly ValueObject\Accessed $accessedTo,
        private readonly ValueObject\AccountType $accountType,
        private readonly ValueObject\AccountId $accountId,
        private readonly ValueObject\Search\Keyword $keyword,
        private readonly ValueObject\NavigationType $navigationType,
        private readonly ValueObject\Id $cursorId,
        private readonly ValueObject\Accessed $cursorAccessed,
        private readonly int $limit = self::DEFAULT_LIMIT,
    ) {
        // 処理なし
    }

    /**
     * @return \App\Domain\Log\PageAccessLogs\ValueObject\Accessed
     */
    public function getAccessedFrom(): ValueObject\Accessed
    {
        return $this->accessedFrom;
    }

    /**
     * @return \App\Domain\Log\PageAccessLogs\ValueObject\Accessed
     */
    public function getAccessedTo(): ValueObject\Accessed
    {
        return $this->accessedTo;
    }

    /**
     * @return \App\Domain\Log\PageAccessLogs\ValueObject\AccountType
     */
    public function getAccountType(): ValueObject\AccountType
    {
        return $this->accountType;
    }

    /**
     * @return \App\Domain\Log\PageAccessLogs\ValueObject\AccountId
     */
    public function getAccountId(): ValueObject\AccountId
    {
        return $this->accountId;
    }

    /**
     * @return \App\Domain\Log\PageAccessLogs\ValueObject\Search\Keyword
     */
    public function getKeyword(): ValueObject\Search\Keyword
    {
        return $this->keyword;
    }

    /**
     * @return \App\Domain\Log\PageAccessLogs\ValueObject\NavigationType
     */
    public function getNavigationType(): ValueObject\NavigationType
    {
        return $this->navigationType;
    }

    /**
     * @return \App\Domain\Log\PageAccessLogs\ValueObject\Id
     */
    public function getCursorId(): ValueObject\Id
    {
        return $this->cursorId;
    }

    /**
     * @return \App\Domain\Log\PageAccessLogs\ValueObject\Accessed
     */
    public function getCursorAccessed(): ValueObject\Accessed
    {
        return $this->cursorAccessed;
    }

    /**
     * @return int
     */
    public function getLimit(): int
    {
        return $this->limit;
    }
}
