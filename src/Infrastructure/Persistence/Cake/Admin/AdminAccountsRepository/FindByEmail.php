<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Cake\Admin\AdminAccountsRepository;

use App\Domain\Admin\AdminAccounts\Entity\AdminAccount as DomainEntity;
use App\Domain\Admin\AdminAccounts\ValueObject as Vo;
use App\Model\Table\Admin\AdminAccountsTable;
use Cake\ORM\Locator\LocatorAwareTrait;

final class FindByEmail
{
    use LocatorAwareTrait;

    /**
     * @var \App\Model\Table\Admin\AdminAccountsTable
     */
    private AdminAccountsTable $table;

    /**
     * @var \App\Infrastructure\Persistence\Cake\Admin\AdminAccountsRepository\Mapper
     */
    private Mapper $mapper;

    /**
     * @param \App\Domain\Admin\AdminAccounts\ValueObject\Email $email
     */
    public function __construct(
        private readonly Vo\Email $email,
    ) {
        $this->table = $this->fetchTable(AdminAccountsTable::class);
        $this->mapper = new Mapper();
    }

    /**
     * @return ?\App\Domain\Admin\AdminAccounts\Entity\AdminAccount
     */
    public function run(): ?DomainEntity
    {
        /** @var \App\Model\Entity\Admin\AdminAccount|null $ormEntity */
        $ormEntity = $this->table
            ->find()
            ->contain(['AccountStatusMasters'])
            ->where([
                'AdminAccounts.email' => $this->email->toString(),
            ])
            ->first();

        return $ormEntity === null ? null : $this->mapper->toDomainEntity($ormEntity);
    }
}
