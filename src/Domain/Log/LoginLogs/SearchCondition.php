<?php
declare(strict_types=1);

namespace App\Domain\Log\LoginLogs;

class SearchCondition
{
    /**
     * @param \App\Domain\Log\LoginLogs\ValueObject\LoginId $login_id
     * @param \App\Domain\Log\LoginLogs\ValueObject\LoginResult $loginResult
     * @param \App\Domain\Log\LoginLogs\ValueObject\LoggedInAt $loggedInAtFrom
     * @param \App\Domain\Log\LoginLogs\ValueObject\LoggedInAt $loggedInAtTo
     * @param \App\Domain\Log\LoginLogs\ValueObject\ImpersonatorAccountId $impersonatorAccountId
     * @param \App\Domain\Log\LoginLogs\ValueObject\Search\Keyword $keyword
     * @param \App\Domain\Log\LoginLogs\ValueObject\LoginActorType $loginActorType
     */
    public function __construct(
        private readonly ValueObject\LoginId $login_id,
        private readonly ValueObject\LoginResult $loginResult,
        private readonly ValueObject\LoggedInAt $loggedInAtFrom,
        private readonly ValueObject\LoggedInAt $loggedInAtTo,
        private readonly ValueObject\ImpersonatorAccountId $impersonatorAccountId,
        private readonly ValueObject\Search\Keyword $keyword,
        private readonly ValueObject\LoginActorType $loginActorType,
    ) {
        // 処理なし
    }

    /**
     * @return \App\Domain\Log\LoginLogs\ValueObject\LoginId
     */
    public function getLoginId(): ValueObject\LoginId
    {
        return $this->login_id;
    }

    /**
     * @return \App\Domain\Log\LoginLogs\ValueObject\LoginResult
     */
    public function getLoginResult(): ValueObject\LoginResult
    {
        return $this->loginResult;
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

    /**
     * @return \App\Domain\Log\LoginLogs\ValueObject\ImpersonatorAccountId
     */
    public function getImpersonatorAccountId(): ValueObject\ImpersonatorAccountId
    {
        return $this->impersonatorAccountId;
    }

    /**
     * @return \App\Domain\Log\LoginLogs\ValueObject\LoginActorType
     */
    public function getLoginActorType(): ValueObject\LoginActorType
    {
        return $this->loginActorType;
    }
}
