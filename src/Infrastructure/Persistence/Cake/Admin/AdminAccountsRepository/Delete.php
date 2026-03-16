<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Cake\Admin\AdminAccountsRepository;

use App\Domain\Admin\AdminAccounts\Entity\AdminAccount as DomainEntity;
use App\Domain\Admin\AdminAccounts\ValueObject as Vo;
use App\Infrastructure\Persistence\Cake\Admin\AdminAccountMapper;
use App\Model\Table\Admin\AdminAccountsTable;
use Cake\ORM\Locator\LocatorAwareTrait;

final class Delete
{
    use LocatorAwareTrait;

    /**
     * @var \App\Model\Table\Admin\AdminAccountsTable
     */
    private AdminAccountsTable $table;

    /**
     * @var \App\Infrastructure\Persistence\Cake\Admin\AdminAccountMapper
     */
    private AdminAccountMapper $mapper;

    /**
     * @param \App\Domain\Admin\AdminAccounts\ValueObject\Id $id
     */
    public function __construct(
        private readonly Vo\Id $id,
    ) {
        $this->table = $this->fetchTable(AdminAccountsTable::class);
        $this->mapper = new AdminAccountMapper();
    }

    /**
     * @return \App\Domain\Admin\AdminAccounts\Entity\AdminAccount
     */
    public function run(): DomainEntity
    {
        $ormEntity = $this->table->get($this->id->toString());

        $this->table->deleteOrFail($ormEntity);

        return $this->mapper->toDomainEntity($ormEntity);
    }
}
