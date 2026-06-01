<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Cake\User\UserGrant\UserGrantPermissionRepository;

use App\Domain\Shared\Enum as SEn;
use App\Domain\Admin\AdminGrant\SearchAdminGrantPermissionCondition as SearchCondition;
use App\Model\Entity\Grant\GrantPermission as OrmEntityGrantPermission;
use App\Model\Table\Grant\GrantPermissionsTable;
use Cake\Database\Expression\QueryExpression;
use Cake\ORM\Locator\LocatorAwareTrait;

final class Search
{
    use LocatorAwareTrait;

    public const ACCOUNT_TYPE = SEn\AccountType::USER->value;

    private SearchCondition $condition;

    private GrantPermissionsTable $table;

    public function __construct(SearchCondition $condition)
    {
        $this->condition = $condition;
        $this->table = $this->fetchTable(GrantPermissionsTable::class);
    }

    /**
     * @return array<\App\Domain\User\UserGrant\Entity\GrantPermission>
     */
    public function run(): array
    {
        $query = $this->table->find()
            ->where([
                'GrantPermissions.account_type' => self::ACCOUNT_TYPE,
                'GrantPermissions.is_active' => $this->condition->getIsActive()->toInt(),
            ])
            ->orderBy([
                'GrantPermissions.sort' => 'ASC',
                'GrantPermissions.id' => 'ASC',
            ]);

        if ($this->condition->getSearchText()->toString() !== '') {
            $query->where(new QueryExpression(
                'MATCH(GrantPermissions.search_text) AGAINST(:search_text IN NATURAL LANGUAGE MODE)',
            ))->bind(':search_text', $this->condition->getSearchText()->toString(), 'string');
        }

        /** @var list<OrmEntityGrantPermission> $rows */
        $rows = $query->all()->toArray();

        return array_map(function (OrmEntityGrantPermission $row) {
            return Mapper::toDomainGrantPermission($row);
        }, $rows);
    }
}
