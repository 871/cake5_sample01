<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Cake\Admin\AdminGrant\AdminAccountGrantRepository;

use App\Domain\Admin\AdminGrant\Entity\AdminAccountGrant;
use App\Domain\Exception\RepositoryException;
use App\Domain\Shared\Enum as SEn;
use App\Model\Table\Admin\AdminAccountsTable;
use App\Model\Table\Grant\GrantPermissionsTable;
use App\Model\Table\Grant\GrantRolesTable;
use Cake\ORM\Locator\LocatorAwareTrait;
use Cake\ORM\Exception\PersistenceFailedException;

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
     * @param \App\Domain\Admin\AdminGrant\Entity\AdminAccountGrant $adminAccountGrant
     */
    public function __construct(
        private readonly AdminAccountGrant $adminAccountGrant,
    ) {
        $this->table = $this->fetchTable(AdminAccountsTable::class);
        $this->grantRolesTable = $this->fetchTable(GrantRolesTable::class);
        $this->grantPermissionsTable = $this->fetchTable(GrantPermissionsTable::class);
    }

    /**
     * @return \App\Domain\Admin\AdminGrant\Entity\AdminAccountGrant
     */
    public function run(): AdminAccountGrant
    {
        try {
            $this->table->getConnection()->transactional(function (): void {
                $accountId = $this->adminAccountGrant->adminAccountId()->toString();
                $accountIdInt = $this->adminAccountGrant->adminAccountId()->toIntOrNull();
                $now = date('Y-m-d H:i:s');

                $this->table->find()
                    ->select(['AdminAccounts.id'])
                    ->where([
                        'AdminAccounts.id' => $accountId,
                    ])
                    ->epilog('FOR UPDATE')
                    ->firstOrFail();

                $selectedGrantRoleIds = array_values(array_unique(array_filter(array_map(
                    fn($grantAccountRole) => $grantAccountRole->grantRoleId()->toIntOrNull(),
                    $this->adminAccountGrant->grantAccountRoles(),
                ))));
                $selectedGrantPermissionIds = array_values(array_unique(array_filter(array_map(
                    fn($grantAccountPermission) => $grantAccountPermission->grantPermissionId()->toIntOrNull(),
                    $this->adminAccountGrant->grantAccountPermissions(),
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

                foreach ($grantRoleIds as $grantRoleId) {
                    $entity = $this->table->GrantAccountRoles->newEmptyEntity();
                    $entity->set('id', \App\Lib\UUID\UUID::uuid4(), ['guard' => false]);
                    $entity->set('account_type', self::ACCOUNT_TYPE);
                    $entity->set('account_id', $accountIdInt);
                    $entity->set('grant_role_id', $grantRoleId);
                    $entity->set('created', $now);
                    $entity->set('modified', $now);
                    $this->table->GrantAccountRoles->saveOrFail($entity, [
                        'checkExisting' => false,
                    ]);
                }

                foreach ($grantPermissionIds as $grantPermissionId) {
                    $entity = $this->table->GrantAccountPermissions->newEmptyEntity();
                    $entity->set('id', \App\Lib\UUID\UUID::uuid4(), ['guard' => false]);
                    $entity->set('account_type', self::ACCOUNT_TYPE);
                    $entity->set('account_id', $accountIdInt);
                    $entity->set('grant_permission_id', $grantPermissionId);
                    $entity->set('created', $now);
                    $entity->set('modified', $now);
                    $this->table->GrantAccountPermissions->saveOrFail($entity, [
                        'checkExisting' => false,
                    ]);
                }
            });
        } catch (PersistenceFailedException $ex) {
            throw new RepositoryException(
                message: 'AdminAccountGrantRepository Save Error',
                previous: $ex,
            );
        }

        return $this->adminAccountGrant;
    }
}
