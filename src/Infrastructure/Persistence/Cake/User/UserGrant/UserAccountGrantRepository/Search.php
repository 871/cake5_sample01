<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Cake\User\UserGrant\UserAccountGrantRepository;

use App\Domain\User\UserGrant\SearchUserAccountGrantCondition;
use App\Domain\User\UserGrant\ValueObject\AccountStatusMasterId;
use App\Domain\User\UserGrant\ValueObject\GrantPermissionId;
use App\Domain\User\UserGrant\ValueObject\GrantRoleId;
use App\Domain\User\UserGrant\ValueObject\UserAccountId;
use App\Domain\Shared\Enum as SEn;
use App\Model\Table\User\UserAccountsTable;
use Cake\Database\Expression\QueryExpression;
use Cake\ORM\Locator\LocatorAwareTrait;
use Cake\ORM\Query\SelectQuery;

final class Search
{
    use LocatorAwareTrait;

    public const ACCOUNT_TYPE = SEn\AccountType::USER->value;

    /**
     * @var \App\Model\Table\User\UserAccountsTable
     */
    private UserAccountsTable $table;

    /**
     * コンストラクタ
     */
    public function __construct()
    {
        $this->table = $this->fetchTable(UserAccountsTable::class);
    }

    /**
     * @param \App\Domain\User\UserGrant\SearchUserAccountGrantCondition $condition
     * @return \Cake\ORM\Query\SelectQuery<\App\Model\Entity\User\UserAccount>
     */
    public function run(SearchUserAccountGrantCondition $condition): array
    {
        return $this->table
            ->find()
            ->select([
                'UserAccounts.id',
                'UserAccounts.email',
                'UserAccounts.name',
                'UserAccounts.account_status_master_id',
                'account_status_master_code' => 'AccountStatusMasters.code',
                'account_status_master_name' => 'AccountStatusMasters.name',
                'grant_permission_id' => 'GrantPermissions.id',
                'grant_permission_name' => 'GrantPermissions.name',
                'grant_permission_code' => 'GrantPermissions.code',
                'role_grant_exists' => 'COALESCE(RoleGrantExists.role_grant_exists, 0)',
                'account_grant_exists' => 'COALESCE(AccoutGrantExists.account_grant_exists, 0)',
            ])
            ->join([
                'AccountStatusMasters' => [
                    'table' => 'account_status_masters',
                    'type' => 'INNER',
                    'conditions' => 'AccountStatusMasters.id = UserAccounts.account_status_master_id',
                ],
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
                        'EXISTS('
                        . ' SELECT 1 FROM grant_role_permissions AS T1'
                        . ' INNER JOIN grant_account_roles AS T2'
                        . ' ON T1.account_type = GrantPermissions.account_type'
                        . ' AND T1.grant_permission_id = GrantPermissions.id'
                        . ' AND T2.grant_role_id = T1.grant_role_id'
                        . ' AND T2.account_id = UserAccounts.id'
                        . ' AND T2.account_type = T1.account_type'
                        . ')',
                    ],
                ],
                'AccoutGrantExists' => [
                    'table' => '(SELECT 1 AS account_grant_exists FROM DUAL)',
                    'type' => 'LEFT',
                    'conditions' => [
                        'EXISTS('
                        . ' SELECT 1 FROM grant_account_permissions AS T3'
                        . ' WHERE T3.account_type = GrantPermissions.account_type'
                        . ' AND T3.grant_permission_id = GrantPermissions.id'
                        . ' AND T3.account_id = UserAccounts.id'
                        . ')',
                    ],
                ],
            ])
            ->where(array_filter(
                [
                    'UserAccounts.id IN' => array_map(
                        fn(UserAccountId $vo): int => $vo->toInt(),
                        $condition->getUserAccountIds(),
                    ),
                    'UserAccounts.account_status_master_id IN' => array_map(
                        fn(AccountStatusMasterId $vo): int => $vo->toInt(),
                        $condition->getAccountStatusMasterIds(),
                    ),
                    'GrantPermissions.id IN' => array_map(
                        fn(GrantPermissionId $vo): int => $vo->toInt(),
                        $condition->getGrantPermissionIds(),
                    ),
                    (function () use ($condition) {
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
                                                '_exists' => '1',
                                            ])
                                            ->where([
                                                'GrantAccountRoles.account_type' => self::ACCOUNT_TYPE,
                                                'GrantAccountRoles.account_id = UserAccounts.id',
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
