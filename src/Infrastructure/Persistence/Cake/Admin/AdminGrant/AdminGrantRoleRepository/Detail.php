<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Cake\Admin\AdminGrant\AdminGrantRoleRepository;

use App\Model\Table\Grant\GrantRolesTable;
use App\Domain\Admin\AdminGrant\Entity\GrantRole as DomainEntity;
use App\Domain\Admin\AdminGrant\ValueObject as Vo;
use Cake\ORM\Locator\LocatorAwareTrait;

final class Detail
{
    use LocatorAwareTrait;

    /**
     * @var \App\Model\Table\Grant\GrantRolesTable
     */
    private GrantRolesTable $table;

    /**
     * @var \App\Infrastructure\Persistence\Cake\Admin\AdminGrant\AdminGrantRoleRepository\Mapper
     */
    private Mapper $mapper;

    /**
     * @param \DateTimeInterface $datetime
     */
    public function __construct(
        private readonly \DateTimeInterface $datetime,
    ) {
        $this->table = $this->fetchTable(GrantRolesTable::class);
        $this->mapper = new Mapper($this->datetime);
    }

    /**
     * @param \App\Domain\Admin\AdminGrant\ValueObject\GrantRoleId $id
     * @return \App\Domain\Admin\AdminGrant\Entity\GrantRole $domainEntity
     */
    public function run(Vo\GrantRoleId $id): DomainEntity
    {
        $ormGrantRole = $this->table->find()
            ->contain([
                'GrantAccountRoles' => [
                    'AdminAccounts',
                ],
                'GrantRolePermissions' => [
                    'GrantPermissions',
                ],
            ])
            ->where([
                'id' => $id->toString(),
            ])
            ->firstOrFail();

        return $this->mapper->toDomainGrantRole($ormGrantRole);
    }
}
