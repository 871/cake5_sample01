<?php
declare(strict_types=1);

namespace App\Domain\Log\LoginLogs;

use App\Domain\Log\LoginLogs\ValueObject;

class SearchCondition
{
    /**
     * @param array<ValueObject\LoginActorType> $loginActorType
     * @param ValueObject\AccountId $accountId
     * @param ValueObject\ImpersonatorAccountId $impersonatorAccountId
     * @param array<ValueObject\LoginResult> $loginResult
     * @param array<ValueObject\FailureReasonCode> $failureReasonCode
     * @param ValueObject\LoggedInAt $loggedInAtFrom
     * @param ValueObject\LoggedInAt $loggedInAtTo
     * @param ValueObject\Search\Keyword $keyword
     */
    public function __construct(
        /** @var array<ValueObject\LoginActorType> */
        private readonly array $loginActorType,

        private readonly ValueObject\AccountId $accountId,
        private readonly ValueObject\ImpersonatorAccountId $impersonatorAccountId,

        /** @var array<ValueObject\LoginResult> */
        private readonly array $loginResult,

        /** @var array<ValueObject\FailureReasonCode> */
        private readonly array $failureReasonCode,

        private readonly ValueObject\LoggedInAt $loggedInAtFrom,
        private readonly ValueObject\LoggedInAt $loggedInAtTo,
        private readonly ValueObject\Search\Keyword $keyword,
    ) {
        // 処理なし
    }

    /**
     * @return array<ValueObject\LoginActorType>
     */
    public function getLoginActorType(): array
    {
        return $this->loginActorType;
    }

    /**
     * @return \App\Domain\Log\LoginLogs\ValueObject\AccountId
     */
    public function getAccountId(): ValueObject\AccountId
    {
        return $this->accountId;
    }

    /**
     * @return \App\Domain\Log\LoginLogs\ValueObject\ImpersonatorAccountId
     */
    public function getImpersonatorAccountId(): ValueObject\ImpersonatorAccountId
    {
        return $this->impersonatorAccountId;
    }

    /**
     * @return array<ValueObject\LoginResult>
     */
    public function getLoginResult(): array
    {
        return $this->loginResult;
    }

    /**
     * @return array<ValueObject\FailureReasonCode>
     */
    public function getFailureReasonCode(): array
    {
        return $this->failureReasonCode;
    }

    /**
     * @return \App\Domain\Log\LoginLogs\ValueObject\LoggedInAt
     */
    public function getLoggedInAtFrom(): ValueObject\LoggedInAt
    {
        return $this->loggedInAtFrom;
    }

    /**
     * @return \App\Domain\Log\LoginLogs\ValueObject\LoggedInAt
     */
    public function getLoggedInAtTo(): ValueObject\LoggedInAt
    {
        return $this->loggedInAtTo;
    }

    /**
     * @return \App\Domain\Log\LoginLogs\ValueObject\Search\Keyword
     */
    public function getKeyword(): ValueObject\Search\Keyword
    {
        return $this->keyword;
    }
}
