<?php
declare(strict_types=1);

namespace App\Domain\Log\LoginLogs\Entity;

use App\Domain\Log\LoginLogs\ValueObject as Vo;
use App\Domain\Shared\ValueObject as SVo;

final class LoginLog
{
    /**
     * @param ?string $id
     * @param ?string $login_id
     * @param ?string $login_actor_type
     * @param ?string $account_id
     * @param ?string $impersonator_account_id
     * @param ?string $login_result
     * @param ?string $ip_address
     * @param ?string $user_agent
     * @param ?string $failure_reason_code
     * @param ?string $logged_in_at
     * @param ?string $created
     */
    public function __construct(
        private readonly ?string $id,
        private readonly ?string $login_id,
        private readonly ?string $login_actor_type,
        private readonly ?string $account_id,
        private readonly ?string $impersonator_account_id,
        private readonly ?string $login_result,
        private readonly ?string $ip_address,
        private readonly ?string $user_agent,
        private readonly ?string $failure_reason_code,
        private readonly ?string $logged_in_at,
        private readonly ?string $created,
    ) {
    }

    /**
     * @return \App\Domain\Log\LoginLogs\ValueObject\Id
     */
    public function id(): Vo\Id
    {
        return Vo\Id::fromString($this->id);
    }

    /**
     * @return \App\Domain\Log\LoginLogs\ValueObject\LoginId
     */
    public function loginId(): Vo\LoginId
    {
        return Vo\LoginId::fromString($this->login_id);
    }

    /**
     * @return \App\Domain\Log\LoginLogs\ValueObject\LoginActorType
     */
    public function loginActorType(): Vo\LoginActorType
    {
        return Vo\LoginActorType::fromString($this->login_actor_type);
    }

    /**
     * @return \App\Domain\Log\LoginLogs\ValueObject\AccountId
     */
    public function accountId(): Vo\AccountId
    {
        return new Vo\AccountId($this->account_id);
    }

    /**
     * @return \App\Domain\Log\LoginLogs\ValueObject\ImpersonatorAccountId
     */
    public function impersonatorAccountId(): Vo\ImpersonatorAccountId
    {
        return new Vo\ImpersonatorAccountId($this->impersonator_account_id);
    }

    /**
     * @return \App\Domain\Log\LoginLogs\ValueObject\LoginResult
     */
    public function loginResult(): Vo\LoginResult
    {
        return Vo\LoginResult::fromString($this->login_result);
    }

    /**
     * @return \App\Domain\Log\LoginLogs\ValueObject\IpAddress
     */
    public function ipAddress(): Vo\IpAddress
    {
        return Vo\IpAddress::fromString($this->ip_address);
    }

    /**
     * @return \App\Domain\Log\LoginLogs\ValueObject\UserAgent
     */
    public function userAgent(): Vo\UserAgent
    {
        return Vo\UserAgent::fromString($this->user_agent);
    }

    /**
     * @return \App\Domain\Log\LoginLogs\ValueObject\FailureReasonCode
     */
    public function failureReasonCode(): Vo\FailureReasonCode
    {
        return Vo\FailureReasonCode::fromString($this->failure_reason_code);
    }

    /**
     * @return \App\Domain\Log\LoginLogs\ValueObject\LoggedInAt
     */
    public function loggedInAt(): Vo\LoggedInAt
    {
        return new Vo\LoggedInAt($this->logged_in_at);
    }

    /**
     * @return \App\Domain\Shared\ValueObject\Created
     */
    public function created(): SVo\Created
    {
        return new SVo\Created($this->created);
    }
}
