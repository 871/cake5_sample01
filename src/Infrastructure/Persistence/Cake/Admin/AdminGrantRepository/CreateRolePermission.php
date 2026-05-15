<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Cake\Admin\AdminGrantRepository;

use App\Domain\Admin\AdminGrant\Entity\GrantRolePermission as DomainEntity;
use App\Domain\Exception\RepositoryException;
use App\Infrastructure\Persistence\Cake\Admin\AdminGrantMapper;
use App\Model\Entity\Grant\GrantRolePermission as OrmEntity;
use App\Model\Table\Grant\GrantRolePermissionsTable;
use Cake\ORM\Exception\PersistenceFailedException;
use Cake\ORM\Locator\LocatorAwareTrait;

final class CreateRolePermission
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
     * @param \App\Domain\Admin\AdminGrant\Entity\GrantRolePermission $domainEntity
     */
    public function __construct(
        private readonly DomainEntity $domainEntity,
    ) {
        $this->table = $this->fetchTable(GrantRolePermissionsTable::class);
        $this->mapper = new AdminGrantMapper();
    }

    /**
     * @return \App\Domain\Admin\AdminGrant\Entity\GrantRolePermission
     */
    public function run(): DomainEntity
    {
        try {
            /** @var \App\Model\Entity\Grant\GrantRolePermission $ormEntity */
            $ormEntity = $this->table->saveOrFail(
                $this->mapper->toNewOrmRolePermissionEntity($this->domainEntity),
                ['checkExisting' => false],
            );
        } catch (PersistenceFailedException $ex) {
            throw new RepositoryException(
                message: 'GrantRolePermission save failed',
                previous: $ex,
            );
        }

        return $this->mapper->toDomainRolePermissionEntity($ormEntity);
    }
}
