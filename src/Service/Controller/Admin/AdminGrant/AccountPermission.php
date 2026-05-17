<?php
declare(strict_types=1);

namespace App\Service\Controller\Admin\AdminGrant;

use App\Domain\Admin\AdminGrant\SearchCondition;
use App\Domain\Admin\AdminGrant\ValueObject\AccountStatusMasterId;
use App\Domain\Admin\AdminGrant\ValueObject\AdminAccountId;
use App\Domain\Admin\AdminGrant\ValueObject\GrantPermissionId;
use App\Domain\Admin\AdminGrant\ValueObject\GrantRoleId;
use App\Infrastructure\Persistence\Cake\Admin\AdminGrantMapper;
use App\Infrastructure\Persistence\Cake\Admin\AdminGrantRepository;
use App\Model\Entity\Grant\GrantAccountPermission;
use App\Model\Entity\Grant\GrantAccountRole;
use App\Model\Table\Grant\GrantAccountPermissionsTable;
use App\Model\Table\Grant\GrantAccountRolesTable;
use App\Service\Controller\Admin\AdminGrant as CategoryService;
use App\Service\Controller\Shared\ServiceInterface;
use App\Service\Controller\Shared\ServiceTrait;
use Cake\ORM\Locator\LocatorAwareTrait;
use Cake\ORM\Query\SelectQuery;

final class AccountPermission implements ServiceInterface
{
    use ServiceTrait;
    use LocatorAwareTrait;

    /**
     * @return array<string, string>
     */
    public function getInitParams(): array
    {
        return [];
    }

    /**
     * @return \Cake\ORM\Query\SelectQuery<\App\Model\Entity\Admin\AdminAccount>
     */
    public function getSearchQuery(): SelectQuery
    {
        return (new AdminGrantRepository($this->datetime))->search(new SearchCondition(
            adminAccountIds: $this->toAdminAccountIds($this->request->getQuery('admin_account_id')),
            accountStatusMasterIds: $this->toAccountStatusMasterIds($this->request->getQuery('account_status_master_id')),
            grantRoleIds: $this->toGrantRoleIds($this->request->getQuery('grant_role_id')),
            grantPermissionIds: $this->toGrantPermissionIds($this->request->getQuery('grant_permission_id')),
        ));
    }

    /**
     * @return array<string, mixed>
     */
    public function getPaginateSettings(): array
    {
        return [
            'limit' => 50,
            'maxLimit' => 200,
            'sortableFields' => [
                'AdminAccounts.id',
                'AdminAccounts.email',
                'AdminAccounts.name',
            ],
            'order' => [
                'AdminAccounts.id' => 'ASC',
            ],
        ];
    }

    /**
     * @return array<int, array<string, string>>
     */
    public function getGrantRoleOptions(): array
    {
        /** @var \App\Service\Controller\Admin\AdminGrant $category */
        $category = $this->createService(CategoryService::class);

        return $category->getGrantRoleOptions();
    }

    /**
     * @return array<int, array<string, string>>
     */
    public function getGrantPermissionOptions(): array
    {
        /** @var \App\Service\Controller\Admin\AdminGrant $category */
        $category = $this->createService(CategoryService::class);

        return $category->getGrantPermissionOptions();
    }

    /**
     * @return array<int, array<string, string>>
     */
    public function getAccountStatusOptions(): array
    {
        /** @var \App\Service\Controller\Admin\AdminGrant $category */
        $category = $this->createService(CategoryService::class);

        return $category->getAccountStatusOptions();
    }

    /**
     * @param string $adminAccountId
     * @return array<\App\Domain\Admin\AdminGrant\Entity\GrantPermission>
     */
    public function getAccountPermissions(string $adminAccountId): array
    {
        return (new AdminGrantRepository($this->datetime))->getAccountPermissions(new AdminAccountId($adminAccountId));
    }

    /**
     * @param string $adminAccountId
     * @return array<int, string>
     */
    public function getGrantedRoleIds(string $adminAccountId): array
    {
        /** @var \App\Model\Table\Grant\GrantAccountRolesTable $table */
        $table = $this->fetchTable(GrantAccountRolesTable::class);

        return $table->find()
            ->select(['grant_role_id'])
            ->where([
                'account_type' => AdminGrantMapper::ACCOUNT_TYPE,
                'account_id' => (int)$adminAccountId,
            ])
            ->all()
            ->map(fn(GrantAccountRole $e): string => (string)$e->grant_role_id)
            ->toList();
    }

    /**
     * @param string $adminAccountId
     * @return array<int, string>
     */
    public function getGrantedPermissionIds(string $adminAccountId): array
    {
        /** @var \App\Model\Table\Grant\GrantAccountPermissionsTable $table */
        $table = $this->fetchTable(GrantAccountPermissionsTable::class);

        return $table->find()
            ->select(['grant_permission_id'])
            ->where([
                'account_type' => AdminGrantMapper::ACCOUNT_TYPE,
                'account_id' => (int)$adminAccountId,
            ])
            ->all()
            ->map(fn(GrantAccountPermission $e): string => (string)$e->grant_permission_id)
            ->toList();
    }

    /**
     * @param string $adminAccountId
     * @return void
     */
    public function save(string $adminAccountId): void
    {
        $grantRoleIds = array_map(
            fn(string $value): GrantRoleId => new GrantRoleId($value),
            array_values(array_filter(array_map(
                fn($value): string => (string)$value,
                (array)$this->request->getData('grant_role_ids'),
            ), fn(string $value): bool => $value !== '')),
        );
        $grantPermissionIds = array_map(
            fn(string $value): GrantPermissionId => new GrantPermissionId($value),
            array_values(array_filter(array_map(
                fn($value): string => (string)$value,
                (array)$this->request->getData('grant_permission_ids'),
            ), fn(string $value): bool => $value !== '')),
        );

        (new AdminGrantRepository($this->datetime))->saveAccountGrants(
            new AdminAccountId($adminAccountId),
            $grantRoleIds,
            $grantPermissionIds,
        );
    }

    /**
     * @param mixed $value
     * @return array<\App\Domain\Admin\AdminGrant\ValueObject\AdminAccountId>
     */
    private function toAdminAccountIds(mixed $value): array
    {
        $val = trim((string)$value);

        return $val === '' ? [] : [new AdminAccountId($val)];
    }

    /**
     * @param mixed $value
     * @return array<\App\Domain\Admin\AdminGrant\ValueObject\AccountStatusMasterId>
     */
    private function toAccountStatusMasterIds(mixed $value): array
    {
        $val = trim((string)$value);

        return $val === '' ? [] : [new AccountStatusMasterId($val)];
    }

    /**
     * @param mixed $value
     * @return array<\App\Domain\Admin\AdminGrant\ValueObject\GrantRoleId>
     */
    private function toGrantRoleIds(mixed $value): array
    {
        $val = trim((string)$value);

        return $val === '' ? [] : [new GrantRoleId($val)];
    }

    /**
     * @param mixed $value
     * @return array<\App\Domain\Admin\AdminGrant\ValueObject\GrantPermissionId>
     */
    private function toGrantPermissionIds(mixed $value): array
    {
        $val = trim((string)$value);

        return $val === '' ? [] : [new GrantPermissionId($val)];
    }
}
