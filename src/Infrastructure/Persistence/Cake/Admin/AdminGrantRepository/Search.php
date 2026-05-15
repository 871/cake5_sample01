<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Cake\Admin\AdminGrantRepository;

use App\Domain\Admin\AdminGrant\SearchCondition;
use App\Domain\Admin\AdminGrant\ValueObject\AccountStatusMasterId;
use App\Domain\Admin\AdminGrant\ValueObject\AdminAccountId;
use App\Domain\Admin\AdminGrant\ValueObject\GrantPermissionId;
use App\Domain\Admin\AdminGrant\ValueObject\GrantRoleId;
use App\Infrastructure\Persistence\Cake\Admin\AdminGrantMapper;
use App\Model\Table\Admin\AdminAccountsTable;
use Cake\Database\Expression\QueryExpression;
use Cake\ORM\Locator\LocatorAwareTrait;
use Cake\ORM\Query\SelectQuery;

final class Search
{
    use LocatorAwareTrait;

    /**
     * @var \App\Model\Table\Admin\AdminAccountsTable
     */
    private AdminAccountsTable $table;

    /**
     * @param \App\Domain\Admin\AdminGrant\SearchCondition $condition
     */
    public function __construct(
        private readonly SearchCondition $condition,
    ) {
        $this->table = $this->fetchTable(AdminAccountsTable::class);
    }

    /**
     * @return \Cake\ORM\Query\SelectQuery<\App\Model\Entity\Admin\AdminAccount>
     */
    public function run(): SelectQuery
    {
        $conn = $this->table->getConnection();

        // 直接付与の権限サブクエリ
        $directPermsQuery = $conn->newQuery()
            ->select([
                'account_id' => 'gap.account_id',
                'perm_id' => 'gp.id',
                'perm_name' => 'gp.name',
                'perm_code' => 'gp.code',
            ])
            ->from(['gap' => 'grant_account_permissions'])
            ->join([
                'gp' => [
                    'table' => 'grant_permissions',
                    'type' => 'INNER',
                    'conditions' => [
                        'gp.id = gap.grant_permission_id',
                        'gp.account_type' => AdminGrantMapper::ACCOUNT_TYPE,
                    ],
                ],
            ])
            ->where(['gap.account_type' => AdminGrantMapper::ACCOUNT_TYPE]);

        // ロール経由の権限サブクエリ
        $rolePermsQuery = $conn->newQuery()
            ->select([
                'account_id' => 'gar.account_id',
                'perm_id' => 'gp.id',
                'perm_name' => 'gp.name',
                'perm_code' => 'gp.code',
            ])
            ->from(['gar' => 'grant_account_roles'])
            ->join([
                'grp' => [
                    'table' => 'grant_role_permissions',
                    'type' => 'INNER',
                    'conditions' => [
                        'gar.grant_role_id = grp.grant_role_id',
                        'grp.account_type' => AdminGrantMapper::ACCOUNT_TYPE,
                    ],
                ],
                'gp' => [
                    'table' => 'grant_permissions',
                    'type' => 'INNER',
                    'conditions' => [
                        'gp.id = grp.grant_permission_id',
                        'gp.account_type' => AdminGrantMapper::ACCOUNT_TYPE,
                    ],
                ],
            ])
            ->where(['gar.account_type' => AdminGrantMapper::ACCOUNT_TYPE]);

        // UNION で権限を統合（重複排除）
        $allPermsQuery = $directPermsQuery->union($rolePermsQuery);

        // メインクエリ
        $query = $this->table
            ->find()
            ->select([
                'AdminAccounts.id',
                'AdminAccounts.email',
                'AdminAccounts.name',
                'AdminAccounts.admin_note',
                'AdminAccounts.account_status_master_id',
                'account_status_master_code' => 'AccountStatusMasters.code',
                'account_status_master_name' => 'AccountStatusMasters.name',
                'grant_permission_id' => 'AccountPerms.perm_id',
                'grant_permission_name' => 'AccountPerms.perm_name',
                'grant_permission_code' => 'AccountPerms.perm_code',
            ])
            ->join([
                'AccountStatusMasters' => [
                    'table' => 'account_status_masters',
                    'type' => 'INNER',
                    'conditions' => 'AccountStatusMasters.id = AdminAccounts.account_status_master_id',
                ],
                'AccountPerms' => [
                    'table' => $allPermsQuery,
                    'type' => 'INNER',
                    'conditions' => 'AccountPerms.account_id = AdminAccounts.id',
                ],
            ]);

        // 検索条件を適用
        $adminAccountIds = array_map(
            fn(AdminAccountId $vo): int => $vo->toInt(),
            $this->condition->getAdminAccountIds(),
        );
        $accountStatusMasterIds = array_map(
            fn(AccountStatusMasterId $vo): int => $vo->toInt(),
            $this->condition->getAccountStatusMasterIds(),
        );
        $grantPermissionIds = array_map(
            fn(GrantPermissionId $vo): int => $vo->toInt(),
            $this->condition->getGrantPermissionIds(),
        );
        $grantRoleIds = array_map(
            fn(GrantRoleId $vo): int => $vo->toInt(),
            $this->condition->getGrantRoleIds(),
        );

        $query->where(
            array_filter([
                'AdminAccounts.id IN' => $adminAccountIds ?: null,
                'AdminAccounts.account_status_master_id IN' => $accountStatusMasterIds ?: null,
                'AccountPerms.perm_id IN' => $grantPermissionIds ?: null,
            ], fn($v) => $v !== null),
        );

        // ロール絞り込み（アカウントがそのロールを保持しているか）
        if ($grantRoleIds !== []) {
            $roleFilterSubquery = $conn->newQuery()
                ->select(['1'])
                ->from(['gar_filter' => 'grant_account_roles'])
                ->where([
                    'gar_filter.account_id = AdminAccounts.id',
                    'gar_filter.account_type' => AdminGrantMapper::ACCOUNT_TYPE,
                    'gar_filter.grant_role_id IN' => $grantRoleIds,
                ]);

            $query->where(function (QueryExpression $exp) use ($roleFilterSubquery): QueryExpression {
                return $exp->exists($roleFilterSubquery);
            });
        }

        return $query;
    }
}
