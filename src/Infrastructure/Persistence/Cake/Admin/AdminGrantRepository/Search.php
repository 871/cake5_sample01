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
        // ※ 採用アプローチ: INNER JOIN grant_permissions + EXISTS (UNION ALL) 方式
        //   【比較検討】
        //   - 旧実装(UNION 派生テーブル JOIN): UNION 結果を派生テーブルとしてマテリアライズしてから
        //     JOIN するため、全アカウントの全権限レコードをメモリ上に展開する。大量データで高コスト。
        //   - IN (UNION ALL 相関サブクエリ): MySQL のセミジョイン最適化が適用される場合もあるが、
        //     UNION ALL 内では FirstMatch 戦略が働きにくい。
        //   - EXISTS (UNION ALL 相関サブクエリ) ← 採用:
        //     最初に一致した行で短絡評価され、(account_id, grant_permission_id, account_type) の
        //     インデックスを効率的に活用できる。派生テーブルのマテリアライズを避けられるため、
        //     パフォーマンスが最も優れている。
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
                'grant_permission_id' => 'GrantPermissions.id',
                'grant_permission_name' => 'GrantPermissions.name',
                'grant_permission_code' => 'GrantPermissions.code',
            ])
            ->join([
                'AccountStatusMasters' => [
                    'table' => 'account_status_masters',
                    'type' => 'INNER',
                    'conditions' => 'AccountStatusMasters.id = AdminAccounts.account_status_master_id',
                ],
                'GrantPermissions' => [
                    'table' => 'grant_permissions',
                    'type' => 'INNER',
                    'conditions' => ['GrantPermissions.account_type' => AdminGrantMapper::ACCOUNT_TYPE],
                ],
            ])
            ->where(function (QueryExpression $exp): QueryExpression {
                // 直接付与またはロール経由で権限を保持しているか EXISTS で確認
                return $exp->exists(
                    (function () {
                        return $this->table->getConnection()->newQuery()
                            ->select(['1'])
                            ->from(['gap' => 'grant_account_permissions'])
                            ->where([
                                'gap.grant_permission_id = GrantPermissions.id',
                                'gap.account_type = GrantPermissions.account_type',
                                'gap.account_id = AdminAccounts.id',
                            ])
                            ->unionAll(
                                (function () {
                                    return $this->table->getConnection()->newQuery()
                                        ->select(['1'])
                                        ->from(['grp' => 'grant_role_permissions'])
                                        ->join(['gar' => [
                                            'table' => 'grant_account_roles',
                                            'type' => 'INNER',
                                            'conditions' => [
                                                'grp.grant_role_id = gar.grant_role_id',
                                                'grp.account_type = GrantPermissions.account_type',
                                                'gar.account_type = GrantPermissions.account_type',
                                                'gar.account_id = AdminAccounts.id',
                                            ],
                                        ]])
                                        ->where(['grp.grant_permission_id = GrantPermissions.id']);
                                })(),
                            );
                    })(),
                );
            });

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
                'GrantPermissions.id IN' => $grantPermissionIds ?: null,
            ], fn($v) => $v !== null),
        );

        // ロール絞り込み（アカウントがそのロールを保持しているか）
        if ($grantRoleIds !== []) {
            $query->where(function (QueryExpression $exp) use ($grantRoleIds): QueryExpression {
                return $exp->exists(
                    (function () use ($grantRoleIds) {
                        return $this->table->getConnection()->newQuery()
                            ->select(['1'])
                            ->from(['gar_filter' => 'grant_account_roles'])
                            ->where([
                                'gar_filter.account_id = AdminAccounts.id',
                                'gar_filter.account_type' => AdminGrantMapper::ACCOUNT_TYPE,
                                'gar_filter.grant_role_id IN' => $grantRoleIds,
                            ]);
                    })(),
                );
            });
        }

        return $query;
    }
}
