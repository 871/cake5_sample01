<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Cake\Admin\AdminGrant\AdminGrantPermissionRepository;

use App\Domain\Admin\AdminGrant\SearchAdminGrantPermissionCondition;
use App\Domain\Shared\Enum as SEn;
use App\Model\Table\Grant\GrantPermissionsTable;
use App\Model\Entity\Grant\GrantPermission as OrmEntityGrantPermission;
use Cake\Database\Expression\QueryExpression;
use Cake\ORM\Locator\LocatorAwareTrait;

final class Search
{
    use LocatorAwareTrait;

    const ACCOUNT_TYPE = SEn\AccountType::ADMIN->value;

    /**
     * @var \App\Model\Table\Grant\GrantPermissionsTable
     */
    private GrantPermissionsTable $table;

    /**
     * @param \App\Domain\Admin\AdminGrant\SearchAdminGrantPermissionCondition $condition
     */
    public function __construct(
        private readonly SearchAdminGrantPermissionCondition $condition,
    ) {
        $this->table = $this->fetchTable(GrantPermissionsTable::class);
    }

    /**
     * @return array
     */
    public function run(): array
    {
        $query = $this->table
            ->find()
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

        return array_map(function(OrmEntityGrantPermission $row) {
            return Mapper::toDomainGrantPermission($row);
        }, $query->all()->toArray());
    }
}
