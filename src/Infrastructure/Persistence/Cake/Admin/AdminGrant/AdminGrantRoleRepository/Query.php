<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Cake\Admin\AdminGrant\AdminGrantRoleRepository;

use App\Domain\Admin\AdminGrant\SearchAdminGrantRoleCondition;
use App\Domain\Shared\Enum as SEn;
use App\Model\Table\Grant\GrantRolesTable;
use App\Model\Entity\Grant\GrantRole as OrmEntityGrantRole;
use App\Domain\Admin\AdminGrant\Entity\GrantRole as DomainEntityGrantRole;
use Cake\ORM\Query\SelectQuery;
use Cake\Database\Expression\QueryExpression;
use Cake\ORM\Locator\LocatorAwareTrait;

final class Query
{
    use LocatorAwareTrait;

    const ACCOUNT_TYPE = SEn\AccountType::ADMIN->value;

    /**
     * @var \App\Model\Table\Grant\GrantRolesTable
     */
    private GrantRolesTable $table;

    /**
     * @param \App\Domain\Admin\AdminGrant\SearchAdminGrantRoleCondition $condition
     */
    public function __construct(
        private readonly \DateTimeInterface $datetime,
    ) {
        $this->table = $this->fetchTable(GrantRolesTable::class);
    }

    /**
     * @return \Cake\ORM\Query\SelectQuery<\App\Model\Entity\Grant\GrantRole>
     */
    public function run(SearchAdminGrantRoleCondition $condition): SelectQuery
    {
        $query = $this->table
            ->find()
            ->where([
                'GrantRoles.account_type' => self::ACCOUNT_TYPE,
                'GrantRoles.is_active' => $condition->getIsActive(),
            ])
            ->orderBy([
                'GrantRoles.sort' => 'ASC',
                'GrantRoles.id' => 'ASC',
            ]);

        if ($condition->getSearchText()->toString() !== '') {
            $query->where(new QueryExpression(
                'MATCH(GrantRoles.search_text) AGAINST(:search_text IN NATURAL LANGUAGE MODE)',
            ))->bind(':search_text', $condition->getSearchText()->toString(), 'string');
        }

        return $query;
    }
}
