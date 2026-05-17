<?php
declare(strict_types=1);

namespace App\Service\Controller\Admin\AdminGrant\RolePermission;

use App\Domain\Admin\AdminGrant\SearchRolePermissionCondition;
use App\Domain\Shared\ValueObject\SearchText;
use App\Infrastructure\Persistence\Cake\Admin\AdminGrantRepository;
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
     * @return \Cake\ORM\Query\SelectQuery<\App\Model\Entity\Grant\GrantRolePermission>
     */
    public function getSearchQuery(): SelectQuery
    {
        return (new AdminGrantRepository($this->datetime))->searchRolePermission(
            new SearchRolePermissionCondition(
                searchText: SearchText::fromString((string)$this->request->getQuery('search_text')),
            ),
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function getPaginateSettings(): array
    {
        return [
            'limit' => 20,
            'maxLimit' => 200,
            'sortableFields' => [
                'GrantRolePermissions.grant_role_id',
                'GrantRolePermissions.grant_permission_id',
            ],
            'order' => [
                'GrantRolePermissions.grant_role_id' => 'ASC',
                'GrantRolePermissions.grant_permission_id' => 'ASC',
            ],
        ];
    }
}
