<?php
declare(strict_types=1);

namespace App\Application\Controller\Admin\UserGrant;

use App\Application\Controller\Admin\UserGrant as CategoryService;
use App\Application\Controller\Shared\ServiceInterface;
use App\Application\Controller\Shared\ServiceTrait;
use App\Domain\User\UserGrant\SearchUserAccountGrantCondition;
use App\Domain\User\UserGrant\ValueObject\AccountStatusMasterId;
use App\Domain\User\UserGrant\ValueObject\GrantPermissionId;
use App\Domain\User\UserGrant\ValueObject\GrantRoleId;
use App\Domain\User\UserGrant\ValueObject\UserAccountId;
use App\Infrastructure\Persistence\Cake\User\UserGrant\UserAccountGrantRepository;
use App\Security\Input\Cast;
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
     * @return \Cake\ORM\Query\SelectQuery<\App\Model\Entity\User\UserAccount>
     */
    public function getSearchQuery(): SelectQuery
    {
        return (new UserAccountGrantRepository($this->datetime))->search(new SearchUserAccountGrantCondition(
            userAccountIds: $this->request->getQuery('user_account_id') ? [
                new UserAccountId(Cast::toStringOrNull($this->request->getQuery('user_account_id'))),
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
                'UserAccounts.id',
                'UserAccounts.email',
                'UserAccounts.name',
            ],
            'order' => [
                'UserAccounts.id' => 'ASC',
                'GrantPermissions.id' => 'ASC',
            ],
        ];
    }

    /**
     * @return array<\App\Domain\User\UserGrant\Entity\GrantRole>
     */
    public function getGrantRoleOptions(): array
    {
        /** @var \App\Application\Controller\Admin\UserGrant $category */
        $category = $this->createService(CategoryService::class);

        return $category->getGrantRoleOptions();
    }

    /**
     * @return array<\App\Domain\User\UserGrant\Entity\GrantPermission>
     */
    public function getGrantPermissionOptions(): array
    {
        /** @var \App\Application\Controller\Admin\UserGrant $category */
        $category = $this->createService(CategoryService::class);

        return $category->getGrantPermissionOptions();
    }

    /**
     * @return array<\App\Domain\User\UserGrant\Entity\AccountStatusMaster>
     */
    public function getAccountStatusOptions(): array
    {
        /** @var \App\Application\Controller\Admin\UserGrant $category */
        $category = $this->createService(CategoryService::class);

        return $category->getAccountStatusOptions();
    }
}
