<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Cake\Admin\AdminGrant\AdminGrantRoleRepository;

use App\Domain\Admin\AdminGrant\Entity\GrantRole as DomainEntity;
use App\Domain\Shared\Enum as SEn;
use App\Model\Table\Grant\GrantRolesTable;
use Cake\ORM\Locator\LocatorAwareTrait;
use DateTimeInterface;

final class Create
{
    use LocatorAwareTrait;

    public const ACCOUNT_TYPE = SEn\AccountType::ADMIN->value;

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
        private readonly DateTimeInterface $datetime,
    ) {
        $this->table = $this->fetchTable(GrantRolesTable::class);
        $this->mapper = new Mapper($this->datetime);
    }

    /**
     * @param \App\Domain\Admin\AdminGrant\Entity\GrantRole $domainEntity
     * @return \App\Domain\Admin\AdminGrant\Entity\GrantRole
     */
    public function run(DomainEntity $domainEntity): DomainEntity
    {
        $newEntity = $this->mapper->toNewOrmGrantRole($domainEntity);
        /** @var \App\Model\Entity\Grant\GrantRole $savedEntity */
        $this->table->saveOrFail($newEntity, [
            'checkExisting' => false,
        ]);

        return $this->mapper->toDomainGrantRole($newEntity);
    }
}
