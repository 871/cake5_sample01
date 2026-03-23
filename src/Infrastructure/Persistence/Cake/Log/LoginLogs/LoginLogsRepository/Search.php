<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Cake\Log\LoginLogs\LoginLogsRepository;

use App\Domain\Log\LoginLogs\SearchCondition;
use App\Model\Table\Log\LoginLogsTable;
use Cake\ORM\Locator\LocatorAwareTrait;
use Cake\ORM\Query\SelectQuery;

final class Search
{
    use LocatorAwareTrait;

    /**
     * @var \App\Model\Table\Log\LoginLogsTable
     */
    private LoginLogsTable $table;

    /**
     * @param \App\Domain\Log\LoginLogs\SearchCondition $condition
     */
    public function __construct(
        private readonly SearchCondition $condition,
    ) {
        $this->table = $this->fetchTable(LoginLogsTable::class);
    }

    /**
     * @return \Cake\ORM\Query\SelectQuery<\App\Model\Entity\Log\LoginLog>
     */
    public function run(): SelectQuery
    {
        return $this->table
            ->find()
            ->contain([
                // 'UserAccounts',
                'AdminAccounts',
                'ImpersonatorAdminAccounts',
            ])
            ->where(
                array_filter([
                    'LoginLogs.id' => $this->condition->getLoginId()->toStringOrNull(),
                    'LoginLogs.login_result' => $this->condition->getLoginResult()->toStringOrNull(),
                    'LoginLogs.logged_in_at >='
                        => $this->condition->getLoggedInAtFrom()->toDateTimeOrNull()?->format('Y-m-d H:i:s'),
                    'LoginLogs.logged_in_at <='
                        => $this->condition->getLoggedInAtTo()->toDateTimeOrNull()?->format('Y-m-d H:i:s'),
                    'LoginLogs.impersonator_account_id'
                        => $this->condition->getImpersonatorAccountId()->toStringOrNull(),
                    'LoginLogs.login_actor_type' => $this->condition->getLoginActorType()->toStringOrNull(),
                ], fn($v) => $v !== null),
            );
    }
}
