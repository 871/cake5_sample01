<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Cake\Admin\AdminGrantRepository;

use App\Domain\Admin\AdminGrant\SearchRolePermissionCondition;
use App\Infrastructure\Persistence\Cake\Admin\AdminGrantMapper;
use App\Model\Table\Grant\GrantRolePermissionsTable;
use Cake\Database\Expression\QueryExpression;
use Cake\ORM\Locator\LocatorAwareTrait;
use Cake\ORM\Query\SelectQuery;

final class SearchRolePermission
{
    use LocatorAwareTrait;

    /**
     * @var \App\Model\Table\Grant\GrantRolePermissionsTable
     */
    private GrantRolePermissionsTable $table;

    /**
     * @param \App\Domain\Admin\AdminGrant\SearchRolePermissionCondition $condition
     */
    public function __construct(
        private readonly SearchRolePermissionCondition $condition,
    ) {
        $this->table = $this->fetchTable(GrantRolePermissionsTable::class);
    }

    /**
     * @return \Cake\ORM\Query\SelectQuery<\App\Model\Entity\Grant\GrantRolePermission>
     */
    public function run(): SelectQuery
    {
        $query = $this->table
            ->find()
            ->contain(['GrantRoles', 'GrantPermissions'])
            ->innerJoinWith('GrantRoles', fn(SelectQuery $q): SelectQuery => $q->where([
                'GrantRoles.account_type' => AdminGrantMapper::ACCOUNT_TYPE,
            ]))
            ->where([
                'GrantRolePermissions.account_type' => AdminGrantMapper::ACCOUNT_TYPE,
            ])
            ->orderBy([
                'GrantRolePermissions.grant_role_id' => 'ASC',
                'GrantRolePermissions.grant_permission_id' => 'ASC',
            ]);

        $keyword = trim($this->condition->getSearchText()->toString());
        if ($keyword === '') {
            return $query;
        }

        return $query
            ->where(new QueryExpression(
                'MATCH(GrantRoles.search_text) AGAINST(:keyword IN NATURAL LANGUAGE MODE)',
            ))
            ->bind(':keyword', $keyword, 'string');
    }
}
