<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Cake\User\UserGrant\UserGrantRoleRepository;

use App\Domain\User\UserGrant\SearchUserGrantRoleCondition as SearchCondition; 
use App\Domain\Shared\Enum as SEn;
use App\Model\Entity\Grant\GrantRole as OrmEntityGrantRole;
use App\Model\Table\Grant\GrantRolesTable;
use Cake\Database\Expression\QueryExpression;
use Cake\ORM\Locator\LocatorAwareTrait;
use Cake\ORM\Query\SelectQuery;
use DateTimeInterface;

final class Search
{
    use LocatorAwareTrait;

    public const ACCOUNT_TYPE = SEn\AccountType::USER->value;

    /**
     * @var \App\Model\Table\Grant\GrantRolesTable
     */
    private GrantRolesTable $table;

    /**
     * @var \App\Infrastructure\Persistence\Cake\User\UserGrant\UserGrantRoleRepository\Mapper
     */
    private Mapper $mapper;

    /**
     * @param \DateTimeInterface $datetime
     */
    public function __construct(
        private readonly DateTimeInterface $datetime,
    ) {
        $this->table = $this->fetchTable(GrantRolesTable::class);
        $this->mapper = new Mapper($this->datetime);
    }

    /**
     * @return array<\App\Domain\User\UserGrant\Entity\GrantRole>
     */
    public function run(SearchCondition $condition): array
    {
        $query = $this->table
            ->find()
            ->where([
                'GrantRoles.account_type' => self::ACCOUNT_TYPE,
            ])
            ->orderBy([
                'GrantRoles.sort' => 'ASC',
                'GrantRoles.id' => 'ASC',
            ]);

        // reuse filters similar to admin
        $isActives = array_map(
            fn($isActive) => $isActive->toInt(),
            $condition->getIsActives(),
        );
        if ($isActives !== []) {
            $query->where([
                'GrantRoles.is_active IN' => $isActives,
            ]);
        }

        $grantPermissionIds = array_map(
            fn($grantPermissionId) => $grantPermissionId->toInt(),
            $condition->getGrantPermissionIds(),
        );
        if ($grantPermissionIds !== []) {
            $query
                ->matching('GrantRolePermissions', function (SelectQuery $query) use ($grantPermissionIds) {
                    return $query->where([
                        'GrantRolePermissions.account_type' => self::ACCOUNT_TYPE,
                        'GrantRolePermissions.grant_permission_id IN' => $grantPermissionIds,
                    ]);
                })
                ->distinct(['GrantRoles.id']);
        }

        if ($condition->getSearchText()->toString() !== '') {
            $query->where(new QueryExpression(
                'MATCH(GrantRoles.search_text) AGAINST(:search_text IN NATURAL LANGUAGE MODE)',
            ))->bind(':search_text', $condition->getSearchText()->toString(), 'string');
        }

        /** @var list<\App\Model\Entity\Grant\GrantRole> $rows */
        $rows = $query->all()->toList();

        return array_map(
            fn(OrmEntityGrantRole $row) => $this->mapper->toDomainGrantRole($row),
            $rows,
        );
    }
}
