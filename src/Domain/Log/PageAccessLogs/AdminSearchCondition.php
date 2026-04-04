<?php
declare(strict_types=1);

namespace App\Domain\Log\PageAccessLogs;

class AdminSearchCondition
{
    /**
     * @param \App\Domain\Log\PageAccessLogs\ValueObject\Accessed $accessedFrom
     * @param \App\Domain\Log\PageAccessLogs\ValueObject\Accessed $accessedTo
     * @param \App\Domain\Log\PageAccessLogs\ValueObject\AccountType $accountType
     * @param \App\Domain\Log\PageAccessLogs\ValueObject\AccountId $accountId
     * @param \App\Domain\Log\PageAccessLogs\ValueObject\Search\Keyword $keyword
     */
    public function __construct(
        private readonly ValueObject\Accessed $accessedFrom,
        private readonly ValueObject\Accessed $accessedTo,
        private readonly ValueObject\AccountType $accountType,
        private readonly ValueObject\AccountId $accountId,
        private readonly ValueObject\Search\Keyword $keyword,
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
}
