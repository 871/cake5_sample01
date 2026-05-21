<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Cake\Admin\AdminGrant\AdminAccountGrantRepository;

use App\Domain\Admin\AdminGrant\SearchAdminAccountGrantCondition;
use App\Domain\Admin\AdminGrant\ValueObject\AccountStatusMasterId;
use App\Domain\Admin\AdminGrant\ValueObject\AdminAccountId;
use App\Domain\Admin\AdminGrant\ValueObject\GrantPermissionId;
use App\Domain\Admin\AdminGrant\ValueObject\GrantRoleId;
use App\Domain\Shared\Enum as SEn;
use App\Model\Table\Admin\AdminAccountsTable;
use Cake\Database\Expression\QueryExpression;
use Cake\ORM\Locator\LocatorAwareTrait;
use Cake\ORM\Query\SelectQuery;
use DateTimeInterface;

final class Search
{
    use LocatorAwareTrait;

    const ACCOUNT_TYPE = SEn\AccountType::ADMIN->value;

    /**
     * @var \App\Model\Table\Admin\AdminAccountsTable
     */
    private AdminAccountsTable $table;

    /**
     * @param \DateTimeInterface $datetime
     */
    public function __construct(
        private readonly DateTimeInterface $datetime,
    ) {
        $this->table = $this->fetchTable(AdminAccountsTable::class);
    }

    /**
     * @return \Cake\ORM\Query\SelectQuery<\App\Model\Entity\Admin\AdminAccount>
     */
    public function run(SearchAdminAccountGrantCondition $condition): SelectQuery
    {
        // ※ 採用アプローチ: INNER JOIN grant_permissions + EXISTS (UNION ALL) 方式
        //   【比較検討】
        //   - 旧実装(UNION 派生テーブル JOIN): UNION 結果を派生テーブルとしてマテリアライズしてから
        //     JOIN するため、全アカウントの全権限レコードをメモリ上に展開する。大量データで高コスト。
        //   - IN (UNION ALL 相関サブクエリ): MySQL のセミジョイン最適化が適用される場合もあるが、
        //     UNION ALL 内では FirstMatch 戦略が働きにくい。
        //   - EXISTS (UNION ALL 相関サブクエリ) ← 採用:
        //     最初に一致した行で短絡評価され、各テーブルの複合インデックスを効率的に活用できる。
        //     派生テーブルのマテリアライズを避けられるため、パフォーマンスが最も優れている。
        //
        // 利用想定 INDEX:
        //   admin_accounts            : PRIMARY KEY (id)
        //                               admin_accounts_idx03 (account_status_master_id)
        //   account_status_masters    : PRIMARY KEY (id)
        //   grant_permissions         : grant_permissions_idx01 (account_type, code)
        //                               PRIMARY KEY (id)
        //   grant_account_permissions : grant_account_permissions_idx01 (account_type, account_id, grant_permission_id)
        //   grant_role_permissions    : grant_role_permissions_idx02 (account_type, grant_permission_id)
        //   grant_account_roles       : grant_account_roles_idx01 (account_type, account_id, grant_role_id)
        return $this->table
            ->find()
            ->select([
                'AdminAccounts.id',
                'AdminAccounts.email',
                'AdminAccounts.name',
                'AdminAccounts.admin_note',
                'AdminAccounts.account_status_master_id',
                'account_status_master_code' => 'AccountStatusMasters.code',
                'account_status_master_name' => 'AccountStatusMasters.name',
                'grant_permission_id' => 'GrantPermissions.id',
                'grant_permission_name' => 'GrantPermissions.name',
                'grant_permission_code' => 'GrantPermissions.code',
                'role_grant_exists' => "COALESCE(RoleGrantExists.role_grant_exists, 0)",
                'account_grant_exists' => "COALESCE(AccoutGrantExists.account_grant_exists, 0)",
            ])
            ->join([
                // account_status_masters.PRIMARY KEY (id) を使用
                // admin_accounts.admin_accounts_idx03 (account_status_master_id) を使用
                'AccountStatusMasters' => [
                    'table' => 'account_status_masters',
                    'type' => 'INNER',
                    'conditions' => 'AccountStatusMasters.id = AdminAccounts.account_status_master_id',
                ],
                // grant_permissions.grant_permissions_idx01 (account_type, code) を使用
                'GrantPermissions' => [
                    'table' => 'grant_permissions',
                    'type' => 'INNER',
                    'conditions' => [
                        'GrantPermissions.account_type' => self::ACCOUNT_TYPE,
                    ],
                ],
                'RoleGrantExists' => [
                    'table' => '(SELECT 1 AS role_grant_exists FROM DUAL)',
                    'type' => 'LEFT',
                    'conditions' => [
                        // grant_role_permissions.grant_role_permissions_idx02 (account_type, grant_permission_id)
                        // grant_account_roles.grant_account_roles_idx03 (grant_role_id)
                        'EXISTS(' 
                        . ' SELECT 1 FROM grant_role_permissions AS T1'
                        . ' INNER JOIN grant_account_roles AS T2' 
                        . ' ON T1.account_type = GrantPermissions.account_type'
                        . ' AND T1.grant_permission_id = GrantPermissions.id'
                        . ' AND T2.grant_role_id = T1.grant_role_id'
                        . ' AND T2.account_id = AdminAccounts.id'
                        . ' AND T2.account_type = T1.account_type'
                        . ')'
                    ],
                ],
                'AccoutGrantExists' => [
                    'table' => '(SELECT 1 AS account_grant_exists FROM DUAL)',
                    'type' => 'LEFT',
                    'conditions' => [
                        // grant_account_permissions.grant_account_permissions_idx01 (account_type, account_id, grant_permission_id)
                        'EXISTS('
                        . ' SELECT 1 FROM grant_account_permissions AS T3'
                        . ' WHERE T3.account_type = GrantPermissions.account_type'
                        . ' AND T3.grant_permission_id = GrantPermissions.id'
                        . ' AND T3.account_id = AdminAccounts.id'
                        . ')'
                    ]
                ],
            ])
            ->where(array_filter(
                [
                    // admin_accounts.PRIMARY KEY (id) を使用
                    'AdminAccounts.id IN' => array_map(
                        fn(AdminAccountId $vo): int => $vo->toInt(),
                        $condition->getAdminAccountIds(),
                    ),
                    // admin_accounts.admin_accounts_idx03 (account_status_master_id) を使用
                    'AdminAccounts.account_status_master_id IN' => array_map(
                        fn(AccountStatusMasterId $vo): int => $vo->toInt(),
                        $condition->getAccountStatusMasterIds(),
                    ),
                    // grant_permissions.PRIMARY KEY (id) を使用
                    'GrantPermissions.id IN' => array_map(
                        fn(GrantPermissionId $vo): int => $vo->toInt(),
                        $condition->getGrantPermissionIds(),
                    ),
                    // ロール絞り込み（オプション）: grant_account_roles_idx01 (account_type, account_id, grant_role_id) を使用
                    (function () {
                        $grantRoleIds = array_map(
                            fn(GrantRoleId $vo): int => $vo->toInt(),
                            $condition->getGrantRoleIds(),
                        );
                        return $grantRoleIds !== []
                            ? function (QueryExpression $exp) use ($grantRoleIds): QueryExpression {
                                return $exp->exists(
                                    (function () use ($grantRoleIds) {
                                        return $this->table->GrantAccountRoles->find()
                                            ->select([
                                                '_exists' => '1'
                                            ])
                                            ->where([
                                                // grant_account_roles_idx01 (account_type, account_id, grant_role_id) の列順
                                                'GrantAccountRoles.account_type' => self::ACCOUNT_TYPE,
                                                'GrantAccountRoles.account_id = AdminAccounts.id',
                                                'GrantAccountRoles.grant_role_id IN' => $grantRoleIds,
                                            ]);
                                    })(),
                                );
                            }
                            : null;
                    })(),
                ],
                fn($v) => !in_array($v, [null, [], ''], true),
            ));
    }
}
