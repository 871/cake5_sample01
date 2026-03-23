<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Cake\Log\LoginLogs\LoginLogsRepository;

use App\Domain\Log\LoginLogs\SearchCondition;
use App\Domain\Log\LoginLogs\ValueObject as Vo;
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
                    'LoginLogs.logged_in_at >='
                        => $this->condition->getLoggedInAtFrom()->toDateTimeOrNull()?->format('Y-m-d\TH:i:s'),
                    'LoginLogs.logged_in_at <='
                        => $this->condition->getLoggedInAtTo()->toDateTimeOrNull()?->format('Y-m-d\TH:i:s'),
                    'LoginLogs.login_actor_type IN' => array_map(
                        fn(Vo\LoginActorType $vo): string => $vo->toString(),
                        $this->condition->getLoginActorType(),
                    ),
                    'LoginLogs.account_id' => $this->condition->getAccountId()->toStringOrNull(),
                    'LoginLogs.impersonator_account_id'
                        => $this->condition->getImpersonatorAccountId()->toStringOrNull(),
                    'LoginLogs.login_result IN' => array_map(
                        fn(Vo\LoginResult $vo): string => $vo->toString(),
                        $this->condition->getLoginResult(),
                    ),
                    'LoginLogs.failure_reason_code IN' => array_map(
                        fn(Vo\FailureReasonCode $vo): string => $vo->toString(),
                        $this->condition->getFailureReasonCode(),
                    ),
                    array_filter([
                        'OR' => array_map(function(string $likeWord) {
                            return [
                                'OR' => [
                                    'LoginLogs.login_id LIKE' => $likeWord,
                                    'LoginLogs.ip_address LIKE' => $likeWord,
                                    'LoginLogs.user_agent LIKE' => $likeWord,
                                    // TODO 未実装 'UserAccounts.account_name LIKE' => $likeWord,
                                    'AdminAccounts.account_name LIKE' => $likeWord,
                                    'ImpersonatorAdminAccounts.account_name LIKE' => $likeWord,
                                ],
                            ];
                        }, $this->condition->getKeyword()->toQueryLikeList()),
                    ], fn($v) => !in_array($v, [null, '', []], true))                    
                ], fn($v) => !in_array($v, [null, '', []], true)),
            );
    }
}
