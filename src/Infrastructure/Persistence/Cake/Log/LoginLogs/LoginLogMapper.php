<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Cake\Log\LoginLogs;

use App\Domain\Log\LoginLogs\Entity\LoginLog as DomainEntity;
use App\Model\Entity\Log\LoginLog as OrmEntity;
use App\Model\Table\Log\LoginLogsTable;
use App\Security\Input\Cast;
use Cake\ORM\Locator\LocatorAwareTrait;

final class LoginLogMapper
{
    use LocatorAwareTrait;

    /**
     * @var \App\Model\Table\Log\LoginLogsTable
     */
    private LoginLogsTable $table;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->table = $this->fetchTable(LoginLogsTable::class);
    }

    /**
     * @param \App\Domain\Log\LoginLogs\Entity\LoginLog $domainEntity
     * @return \App\Model\Entity\Log\LoginLog
     */
    public function toNewOrmEntity(DomainEntity $domainEntity): OrmEntity
    {
        $entity = $this->table->newEntity([
            'id' => $domainEntity->id()->toString(),
            'login_id' => $domainEntity->loginId()->toString(),
            'login_actor_type' => $domainEntity->loginActorType()->toString(),
            'account_id' => $domainEntity->accountId()->toIntOrNull(),
            'impersonator_account_id' => $domainEntity->impersonatorAccountId()->toIntOrNull(),
            'login_result' => $domainEntity->loginResult()->toString(),
            'ip_address' => $domainEntity->ipAddress()->toString(),
            'user_agent' => $domainEntity->userAgent()->toString(),
            'failure_reason_code' => $domainEntity->failureReasonCode()->toStringOrNull(),
            'logged_in_at' => $domainEntity->loggedInAt()->toDateTimeOrNull()?->format('Y-m-d\TH:i:s'),
            'created' => $domainEntity->created()->toDateTimeOrNull()?->format('Y-m-d\TH:i:s'),
        ], [
            'validate' => false,
        ]);

        return $entity;
    }

    /**
     * @param \App\Model\Entity\Log\LoginLog $ormEntity
     * @return \App\Domain\Log\LoginLogs\Entity\LoginLog
     */
    public function toDomainEntity(OrmEntity $ormEntity): DomainEntity
    {
        return new DomainEntity(
            id: Cast::toStringOrNull($ormEntity->id),
            login_id: Cast::toStringOrNull($ormEntity->login_id),
            login_actor_type: Cast::toStringOrNull($ormEntity->login_actor_type),
            account_id: Cast::toStringOrNull($ormEntity->account_id),
            impersonator_account_id: Cast::toStringOrNull($ormEntity->impersonator_account_id),
            login_result: Cast::toStringOrNull($ormEntity->login_result),
            ip_address: Cast::toStringOrNull($ormEntity->ip_address),
            user_agent: Cast::toStringOrNull($ormEntity->user_agent),
            failure_reason_code: Cast::toStringOrNull($ormEntity->failure_reason_code),
            logged_in_at: Cast::toStringOrNull($ormEntity->logged_in_at?->format('Y-m-d\TH:i:s')),
            created: Cast::toStringOrNull($ormEntity->created?->format('Y-m-d\TH:i:s')),
        );
    }
}
