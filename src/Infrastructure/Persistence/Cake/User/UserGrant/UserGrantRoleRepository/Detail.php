<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Cake\User\UserGrant\UserGrantRoleRepository;

use App\Domain\User\UserGrant\Entity\GrantRole as DomainEntity;
use App\Domain\User\UserGrant\ValueObject as Vo;
use App\Model\Table\Grant\GrantRolesTable;
use Cake\ORM\Locator\LocatorAwareTrait;
use DateTimeInterface;

final class Detail
{
    use LocatorAwareTrait;

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
     * @param \App\Domain\User\UserGrant\ValueObject\GrantRoleId $id
     * @return \App\Domain\User\UserGrant\Entity\GrantRole $domainEntity
     */
    public function run(Vo\GrantRoleId $id): DomainEntity
    {
        /** @var \App\Model\Entity\Grant\GrantRole $ormGrantRole */
        $ormGrantRole = $this->table->find()
            ->contain([
                'GrantAccountRoles' => [
                    'UserAccounts' => [
                        'AccountStatusMasters',
                    ],
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
