<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Cake\Admin\AdminGrant\AdminAccountGrantRepository;

use App\Domain\Admin\AdminGrant\Entity\AdminAccountGrant;
use App\Domain\Exception\RepositoryException;
use App\Domain\Shared\Enum as SEn;
use App\Lib\UUID\UUID;
use App\Model\Table\Admin\AdminAccountsTable;
use App\Model\Table\Grant\GrantPermissionsTable;
use App\Model\Table\Grant\GrantRolesTable;
use Cake\ORM\Locator\LocatorAwareTrait;
use Cake\ORM\Exception\PersistenceFailedException;
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
                $accountId = $adminAccountGrant->adminAccountId()->toString();
                $accountIdInt = $adminAccountGrant->adminAccountId()->toIntOrNull();
                $now = $this->datetime->format('Y-m-d H:i:s');

                $this->table->find()
                    ->select(['AdminAccounts.id'])
                    ->where([
                        'AdminAccounts.id' => $accountId,
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

                $grantRoleIds = $selectedGrantRoleIds === []
                    ? []
                    : array_map(
                        fn($row) => (int)$row->id,
                        $this->grantRolesTable->find()
                            ->select(['GrantRoles.id'])
                            ->where([
                                'GrantRoles.account_type' => self::ACCOUNT_TYPE,
                                'GrantRoles.is_active' => 1,
                                'GrantRoles.id IN' => $selectedGrantRoleIds,
                            ])
                            ->epilog('FOR UPDATE')
                            ->all()
                            ->toList(),
                    );

                $grantPermissionIds = $selectedGrantPermissionIds === []
                    ? []
                    : array_map(
                        fn($row) => (int)$row->id,
                        $this->grantPermissionsTable->find()
                            ->select(['GrantPermissions.id'])
                            ->where([
                                'GrantPermissions.account_type' => self::ACCOUNT_TYPE,
                                'GrantPermissions.is_active' => 1,
                                'GrantPermissions.id IN' => $selectedGrantPermissionIds,
                            ])
                            ->epilog('FOR UPDATE')
                            ->all()
                            ->toList(),
                    );

                $this->table->GrantAccountRoles->deleteAll([
                    'GrantAccountRoles.account_type' => self::ACCOUNT_TYPE,
                    'GrantAccountRoles.account_id' => $accountId,
                ]);
                $this->table->GrantAccountPermissions->deleteAll([
                    'GrantAccountPermissions.account_type' => self::ACCOUNT_TYPE,
                    'GrantAccountPermissions.account_id' => $accountId,
                ]);

                if ($grantRoleIds !== []) {
                    $this->table->GrantAccountRoles->saveManyOrFail(
                        array_map(
                            function (int $grantRoleId) use ($accountIdInt, $now) {
                                $entity = $this->table->GrantAccountRoles->newEmptyEntity();
                                $entity->set('id', UUID::uuid4(), ['guard' => false]);
                                $entity->set('account_type', self::ACCOUNT_TYPE);
                                $entity->set('account_id', $accountIdInt);
                                $entity->set('grant_role_id', $grantRoleId);
                                $entity->set('created', $now);
                                $entity->set('modified', $now);
                                return $entity;
                            },
                            $grantRoleIds,
                        ),
                        [
                            'checkExisting' => false,
                        ],
                    );
                }

                if ($grantPermissionIds !== []) {
                    $this->table->GrantAccountPermissions->saveManyOrFail(
                        array_map(
                            function (int $grantPermissionId) use ($accountIdInt, $now) {
                                $entity = $this->table->GrantAccountPermissions->newEmptyEntity();
                                $entity->set('id', UUID::uuid4(), ['guard' => false]);
                                $entity->set('account_type', self::ACCOUNT_TYPE);
                                $entity->set('account_id', $accountIdInt);
                                $entity->set('grant_permission_id', $grantPermissionId);
                                $entity->set('created', $now);
                                $entity->set('modified', $now);
                                return $entity;
                            },
                            $grantPermissionIds,
                        ),
                        [
                            'checkExisting' => false,
                        ],
                    );
                }
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
