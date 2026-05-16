<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Cake\Admin\AdminGrantRepository;

use App\Domain\Admin\AdminGrant\Entity\GrantPermission as DomainGrantPermission;
use App\Domain\Admin\AdminGrant\ValueObject\AdminAccountId;
use App\Infrastructure\Persistence\Cake\Admin\AdminGrantMapper;
use App\Model\Entity\Grant\GrantPermission as OrmGrantPermission;
use App\Model\Table\Grant\GrantAccountPermissionsTable;
use App\Model\Table\Grant\GrantAccountRolesTable;
use App\Model\Table\Grant\GrantPermissionsTable;
use App\Model\Table\Grant\GrantRolePermissionsTable;
use Cake\Database\Expression\QueryExpression;
use Cake\ORM\Locator\LocatorAwareTrait;

final class GetAccountPermissions
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
     * @var \App\Model\Table\Grant\GrantPermissionsTable
     */
    private GrantPermissionsTable $permissionsTable;

    /**
     * @var \App\Infrastructure\Persistence\Cake\Admin\AdminGrantMapper
     */
    private AdminGrantMapper $mapper;

    /**
     * @param \App\Domain\Admin\AdminGrant\ValueObject\AdminAccountId $adminAccountId
     */
    public function __construct(
        private readonly AdminAccountId $adminAccountId,
    ) {
        $this->accountRolesTable = $this->fetchTable(GrantAccountRolesTable::class);
        $this->rolePermissionsTable = $this->fetchTable(GrantRolePermissionsTable::class);
        $this->accountPermissionsTable = $this->fetchTable(GrantAccountPermissionsTable::class);
        $this->permissionsTable = $this->fetchTable(GrantPermissionsTable::class);
        $this->mapper = new AdminGrantMapper();
    }

    /**
     * @return \App\Domain\Admin\AdminGrant\Entity\GrantPermission[]
     */
    public function run(): array
    {
        $accountId = $this->adminAccountId->toInt();

        // 権限一覧を取得（ロール経由 OR 個別付与）
        /** @var \App\Model\Entity\Grant\GrantPermission[] $ormEntities */
        $ormEntities = $this->permissionsTable
            ->find()
            ->where([
                'GrantPermissions.account_type' => AdminGrantMapper::ACCOUNT_TYPE,
                'GrantPermissions.is_active' => 1,
            ])
            ->where(function (QueryExpression $exp) use ($accountId): QueryExpression {
                return $exp->or([
                    $exp->in(
                        'GrantPermissions.id',
                        (function () use ($accountId) {
                            return $this->rolePermissionsTable
                                ->find()
                                ->select(['grant_permission_id'])
                                ->where([
                                    'GrantRolePermissions.account_type' => AdminGrantMapper::ACCOUNT_TYPE,
                                    'GrantRolePermissions.grant_role_id IN' => (function () use ($accountId) {
                                        return $this->accountRolesTable
                                            ->find()
                                            ->select(['grant_role_id'])
                                            ->where([
                                                'GrantAccountRoles.account_type' => AdminGrantMapper::ACCOUNT_TYPE,
                                                'GrantAccountRoles.account_id' => $accountId,
                                            ]);
                                    })(),
                                ]);
                        })(),
                    ),
                    $exp->in(
                        'GrantPermissions.id',
                        (function () use ($accountId) {
                            return $this->accountPermissionsTable
                                ->find()
                                ->select(['grant_permission_id'])
                                ->where([
                                    'GrantAccountPermissions.account_type' => AdminGrantMapper::ACCOUNT_TYPE,
                                    'GrantAccountPermissions.account_id' => $accountId,
                                ]);
                        })(),
                    ),
                ]);
            })
            ->orderBy(['GrantPermissions.sort' => 'ASC', 'GrantPermissions.id' => 'ASC'])
            ->all()
            ->toArray();

        return array_map(
            fn(OrmGrantPermission $ormEntity): DomainGrantPermission =>
                $this->mapper->toDomainPermissionEntity($ormEntity),
            $ormEntities,
        );
    }
}
