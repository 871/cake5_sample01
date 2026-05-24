<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Cake\Admin\AdminGrant\AdminAccountGrantRepository;

use App\Domain\Admin\AdminGrant\Entity\AdminAccountGrant;
use App\Domain\Admin\AdminGrant\ValueObject as Vo;
use App\Domain\Exception\RepositoryException;
use App\Domain\Shared\Enum as SEn;
use App\Lib\UUID\UUID;
use App\Model\Entity\Grant\GrantPermission as OrmGrantPermission;
use App\Model\Entity\Grant\GrantRole as OrmGrantRole;
use App\Model\Table\Admin\AdminAccountsTable;
use App\Model\Table\Grant\GrantPermissionsTable;
use App\Model\Table\Grant\GrantRolesTable;
use Cake\ORM\Exception\PersistenceFailedException;
use Cake\ORM\Locator\LocatorAwareTrait;
use DateTimeInterface;

final class Save
{
    use LocatorAwareTrait;

    private const ACCOUNT_TYPE = SEn\AccountType::ADMIN->value;

    /**
     * @var \App\Model\Table\Admin\AdminAccountsTable
     */
    private AdminAccountsTable $table;

    /**
     * @var \App\Model\Table\Grant\GrantRolesTable
     */
    private GrantRolesTable $grantRolesTable;

    /**
     * @var \App\Model\Table\Grant\GrantPermissionsTable
     */
    private GrantPermissionsTable $grantPermissionsTable;

    /**
     * @param \DateTimeInterface $datetime
     */
    public function __construct(
        private readonly DateTimeInterface $datetime,
    ) {
        $this->table = $this->fetchTable(AdminAccountsTable::class);
        $this->grantRolesTable = $this->fetchTable(GrantRolesTable::class);
        $this->grantPermissionsTable = $this->fetchTable(GrantPermissionsTable::class);
    }

    /**
     * @param \App\Domain\Admin\AdminGrant\Entity\AdminAccountGrant $adminAccountGrant
     * @return \App\Domain\Admin\AdminGrant\Entity\AdminAccountGrant
     */
    public function run(AdminAccountGrant $adminAccountGrant): AdminAccountGrant
    {
        try {
            $this->table->getConnection()->transactional(function () use ($adminAccountGrant): void {
                $this->table->find()
                    ->select(['AdminAccounts.id'])
                    ->where([
                        'AdminAccounts.id' => $adminAccountGrant->adminAccountId()->toString(),
                    ])
                    ->epilog('FOR UPDATE')
                    ->firstOrFail();

                $selectedGrantRoleIds = array_values(array_unique(array_filter(array_map(
                    fn($grantAccountRole) => $grantAccountRole->grantRoleId()->toIntOrNull(),
                    $adminAccountGrant->grantAccountRoles(),
                ))));
                $selectedGrantPermissionIds = array_values(array_unique(array_filter(array_map(
                    fn($grantAccountPermission) => $grantAccountPermission->grantPermissionId()->toIntOrNull(),
                    $adminAccountGrant->grantAccountPermissions(),
                ))));

                /** @var list<\App\Model\Entity\Grant\GrantRole> $grantRoleRows */
                $grantRoleRows = $selectedGrantRoleIds === []
                    ? []
                    : $this->grantRolesTable->find()
                        ->select(['GrantRoles.id'])
                        ->where([
                            'GrantRoles.account_type' => self::ACCOUNT_TYPE,
                            'GrantRoles.is_active' => Vo\IsActive::ACTIVE,
                            'GrantRoles.id IN' => $selectedGrantRoleIds,
                        ])
                        ->epilog('FOR UPDATE')
                        ->all()
                        ->toList();
                $grantRoleIds = array_map(
                    fn(OrmGrantRole $row) => (int)$row->id,
                    $grantRoleRows,
                );

                /** @var list<\App\Model\Entity\Grant\GrantPermission> $grantPermissionRows */
                $grantPermissionRows = $selectedGrantPermissionIds === []
                    ? []
                    : $this->grantPermissionsTable->find()
                        ->select(['GrantPermissions.id'])
                        ->where([
                            'GrantPermissions.account_type' => self::ACCOUNT_TYPE,
                            'GrantPermissions.is_active' => Vo\IsActive::ACTIVE,
                            'GrantPermissions.id IN' => $selectedGrantPermissionIds,
                        ])
                        ->epilog('FOR UPDATE')
                        ->all()
                        ->toList();
                $grantPermissionIds = array_map(
                    fn(OrmGrantPermission $row) => (int)$row->id,
                    $grantPermissionRows,
                );

                $this->table->GrantAccountRoles->deleteAll([
                    'GrantAccountRoles.account_type' => self::ACCOUNT_TYPE,
                    'GrantAccountRoles.account_id' => $adminAccountGrant->adminAccountId()->toString(),
                ]);
                $this->table->GrantAccountPermissions->deleteAll([
                    'GrantAccountPermissions.account_type' => self::ACCOUNT_TYPE,
                    'GrantAccountPermissions.account_id' => $adminAccountGrant->adminAccountId()->toString(),
                ]);

                $this->table->GrantAccountRoles->saveManyOrFail(
                    $this->table->GrantAccountRoles->newEntities(
                        array_map(
                            fn(int $grantRoleId): array => [
                                'id' => UUID::uuid7(),
                                'account_type' => self::ACCOUNT_TYPE,
                                'account_id' => $adminAccountGrant->adminAccountId()->toString(),
                                'grant_role_id' => $grantRoleId,
                                'created' => $this->datetime->format('Y-m-d\TH:i:s'),
                                'modified' => $this->datetime->format('Y-m-d\TH:i:s'),
                            ],
                            $grantRoleIds,
                        ),
                        [
                            'accessibleFields' => [
                                'id' => true,
                            ],
                        ],
                    ),
                    [
                        'checkExisting' => false,
                    ],
                );

                $this->table->GrantAccountPermissions->saveManyOrFail(
                    $this->table->GrantAccountPermissions->newEntities(
                        array_map(
                            fn(int $grantPermissionId): array => [
                                'id' => UUID::uuid7(),
                                'account_type' => self::ACCOUNT_TYPE,
                                'account_id' => $adminAccountGrant->adminAccountId()->toString(),
                                'grant_permission_id' => $grantPermissionId,
                                'created' => $this->datetime->format('Y-m-d\TH:i:s'),
                                'modified' => $this->datetime->format('Y-m-d\TH:i:s'),
                            ],
                            $grantPermissionIds,
                        ),
                        [
                            'accessibleFields' => [
                                'id' => true,
                            ],
                        ],
                    ),
                    [
                        'checkExisting' => false,
                    ],
                );
            });
        } catch (PersistenceFailedException $ex) {
            throw new RepositoryException(
                message: 'AdminAccountGrantRepository Save Error',
                previous: $ex,
            );
        }

        return $adminAccountGrant;
    }
}
