<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Cake\Admin\AdminGrantRepository;

use App\Domain\Admin\AdminGrant\ValueObject\AdminAccountId;
use App\Domain\Admin\AdminGrant\ValueObject\GrantPermissionId;
use App\Infrastructure\Persistence\Cake\Admin\AdminGrantMapper;
use App\Model\Table\Grant\GrantAccountPermissionsTable;
use App\Model\Table\Grant\GrantAccountRolesTable;
use App\Model\Table\Grant\GrantRolePermissionsTable;
use Cake\ORM\Locator\LocatorAwareTrait;

final class HasPermission
{
    use LocatorAwareTrait;

    /**
     * @var \App\Model\Table\Grant\GrantAccountRolesTable
     */
    private GrantAccountRolesTable $accountRolesTable;

    /**
     * @var \App\Model\Table\Grant\GrantRolePermissionsTable
     */
    private GrantRolePermissionsTable $rolePermissionsTable;

    /**
     * @var \App\Model\Table\Grant\GrantAccountPermissionsTable
     */
    private GrantAccountPermissionsTable $accountPermissionsTable;

    /**
     * @param \App\Domain\Admin\AdminGrant\ValueObject\AdminAccountId $adminAccountId
     * @param \App\Domain\Admin\AdminGrant\ValueObject\GrantPermissionId $grantPermissionId
     */
    public function __construct(
        private readonly AdminAccountId $adminAccountId,
        private readonly GrantPermissionId $grantPermissionId,
    ) {
        $this->accountRolesTable = $this->fetchTable(GrantAccountRolesTable::class);
        $this->rolePermissionsTable = $this->fetchTable(GrantRolePermissionsTable::class);
        $this->accountPermissionsTable = $this->fetchTable(GrantAccountPermissionsTable::class);
    }

    /**
     * @return bool
     */
    public function run(): bool
    {
        $accountId = $this->adminAccountId->toInt();
        $permissionId = $this->grantPermissionId->toInt();

        // 個別付与の権限チェック
        $hasIndividual = $this->accountPermissionsTable
            ->find()
            ->where([
                'GrantAccountPermissions.account_type' => AdminGrantMapper::ACCOUNT_TYPE,
                'GrantAccountPermissions.account_id' => $accountId,
                'GrantAccountPermissions.grant_permission_id' => $permissionId,
            ])
            ->count() > 0;

        if ($hasIndividual) {
            return true;
        }

        // ロール経由の権限チェック（サブクエリを使用）
        $roleSubQuery = $this->accountRolesTable
            ->find()
            ->select(['grant_role_id'])
            ->where([
                'GrantAccountRoles.account_type' => AdminGrantMapper::ACCOUNT_TYPE,
                'GrantAccountRoles.account_id' => $accountId,
            ]);

        return $this->rolePermissionsTable
            ->find()
            ->where([
                'GrantRolePermissions.account_type' => AdminGrantMapper::ACCOUNT_TYPE,
                'GrantRolePermissions.grant_role_id IN' => $roleSubQuery,
                'GrantRolePermissions.grant_permission_id' => $permissionId,
            ])
            ->count() > 0;
    }
}
