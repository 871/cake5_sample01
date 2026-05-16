<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Cake\Admin\AdminGrantRepository;

use App\Domain\Admin\AdminGrant\Entity\GrantRolePermission as DomainEntity;
use App\Domain\Admin\AdminGrant\ValueObject\GrantRolePermissionId;
use App\Domain\Exception\RepositoryException;
use App\Infrastructure\Persistence\Cake\Admin\AdminGrantMapper;
use App\Model\Table\Grant\GrantRolePermissionsTable;
use Cake\ORM\Locator\LocatorAwareTrait;

final class DeleteRolePermission
{
    use LocatorAwareTrait;

    /**
     * @var \App\Model\Table\Grant\GrantRolePermissionsTable
     */
    private GrantRolePermissionsTable $table;

    /**
     * @var \App\Infrastructure\Persistence\Cake\Admin\AdminGrantMapper
     */
    private AdminGrantMapper $mapper;

    /**
     * @param \App\Domain\Admin\AdminGrant\ValueObject\GrantRolePermissionId $id
     */
    public function __construct(
        private readonly GrantRolePermissionId $id,
    ) {
        $this->table = $this->fetchTable(GrantRolePermissionsTable::class);
        $this->mapper = new AdminGrantMapper();
    }

    /**
     * @return \App\Domain\Admin\AdminGrant\Entity\GrantRolePermission
     */
    public function run(): DomainEntity
    {
        /** @var \App\Model\Entity\Grant\GrantRolePermission $ormEntity */
        $ormEntity = $this->table
            ->find()
            ->where([
                'GrantRolePermissions.id' => $this->id->toString(),
                'GrantRolePermissions.account_type' => AdminGrantMapper::ACCOUNT_TYPE,
            ])
            ->first() ?? throw new RepositoryException(
                'GrantRolePermission data not found'
                . '[id: ' . $this->id->toString() . ']',
            );

        $this->table->deleteOrFail($ormEntity);

        return $this->mapper->toDomainRolePermissionEntity($ormEntity);
    }
}
