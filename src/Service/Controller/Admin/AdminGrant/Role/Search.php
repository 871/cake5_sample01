<?php
declare(strict_types=1);

namespace App\Service\Controller\Admin\AdminGrant\Role;

use App\Domain\Admin\AdminGrant\SearchAdminGrantRoleCondition;
use App\Domain\Admin\AdminGrant\ValueObject\IsActive;
use App\Domain\Shared\ValueObject\SearchText;
use App\Infrastructure\Persistence\Cake\Admin\AdminGrant\AdminGrantRoleRepository;
use App\Security\Input\Cast;
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
        return [
            'is_active' => '1',
        ];
    }

    /**
     * @return \Cake\ORM\Query\SelectQuery<\App\Model\Entity\Grant\GrantRole>
     */
    public function getSearchQuery(): SelectQuery
    {
        $isActive = Cast::toStringOrNull($this->request->getQuery('is_active'));
        if (!in_array($isActive, ['0', '1'], true)) {
            $isActive = '1';
        }

        return (new AdminGrantRoleRepository($this->datetime))->query(new SearchAdminGrantRoleCondition(
            searchText: new SearchText(Cast::toStringOrNull($this->request->getQuery('keyword'))),
            isActive: new IsActive($isActive),
        ));
    }

    /**
     * @return array<string, array<int|string, string>|int>
     */
    public function getPaginateSettings(): array
    {
        return [
            'limit' => 20,
            'maxLimit' => 200,
            'sortableFields' => [
                'GrantRoles.id',
                'GrantRoles.code',
                'GrantRoles.name',
                'GrantRoles.sort',
                'GrantRoles.modified',
            ],
            'order' => [
                'GrantRoles.sort' => 'ASC',
                'GrantRoles.id' => 'ASC',
            ],
        ];
    }
}
