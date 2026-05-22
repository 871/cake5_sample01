<?php
declare(strict_types=1);

namespace App\Service\Controller\Admin\AdminGrant;

use App\Domain\Admin\AdminGrant\SearchAdminAccountGrantCondition;
use App\Domain\Admin\AdminGrant\ValueObject\AccountStatusMasterId;
use App\Domain\Admin\AdminGrant\ValueObject\AdminAccountId;
use App\Domain\Admin\AdminGrant\ValueObject\GrantPermissionId;
use App\Domain\Admin\AdminGrant\ValueObject\GrantRoleId;
use App\Infrastructure\Persistence\Cake\Admin\AdminGrant\AdminAccountGrantRepository;
use App\Security\Input\Cast;
use App\Service\Controller\Admin\AdminGrant as CategoryService;
use App\Service\Controller\Shared\ServiceInterface;
use App\Service\Controller\Shared\ServiceTrait;
use Cake\ORM\Query\SelectQuery;

final class Search implements ServiceInterface
{
    use ServiceTrait;

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
        return (new AdminAccountGrantRepository($this->datetime))->search(new SearchAdminAccountGrantCondition(
            adminAccountIds: $this->request->getQuery('admin_account_id') ? [
                new AdminAccountId(Cast::toStringOrNull($this->request->getQuery('admin_account_id'))),
            ] : [],
            accountStatusMasterIds: array_map(function ($val) {
                return new AccountStatusMasterId(Cast::toStringOrNull($val));
            }, (array)$this->request->getQuery('account_status_master_id', [])),
            grantRoleIds: array_map(function ($val) {
                return new GrantRoleId(Cast::toStringOrNull($val));
            }, (array)$this->request->getQuery('grant_role_id', [])),
            grantPermissionIds: array_map(function ($val) {
                return new GrantPermissionId(Cast::toStringOrNull($val));
            }, (array)$this->request->getQuery('grant_permission_id', [])),
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
                'GrantPermissions.id' => 'ASC',
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
}
