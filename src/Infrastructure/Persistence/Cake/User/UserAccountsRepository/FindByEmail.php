<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Cake\User\UserAccountsRepository;

use App\Domain\User\UserAccounts\Entity\UserAccount as DomainEntity;
use App\Domain\User\UserAccounts\ValueObject as Vo;
use App\Model\Table\User\UserAccountsTable;
use Cake\ORM\Locator\LocatorAwareTrait;

final class FindByEmail
{
    use LocatorAwareTrait;

    /**
     * @var \App\Model\Table\User\UserAccountsTable
     */
    private UserAccountsTable $table;

    /**
     * @var \App\Infrastructure\Persistence\Cake\User\UserAccountsRepository\Mapper
     */
    private Mapper $mapper;

    /**
     * @param \App\Domain\User\UserAccounts\ValueObject\Email $email
     */
    public function __construct(
        private readonly Vo\Email $email,
    ) {
        $this->table = $this->fetchTable(UserAccountsTable::class);
        $this->mapper = new Mapper();
    }

    /**
     * @return ?\App\Domain\User\UserAccounts\Entity\UserAccount
     */
    public function run(): ?DomainEntity
    {
        /** @var \App\Model\Entity\User\UserAccount|null $ormEntity */
        $ormEntity = $this->table
            ->find()
            ->contain(['AccountStatusMasters'])
            ->where([
                'UserAccounts.email' => $this->email->toString(),
            ])
            ->first();

        return $ormEntity === null ? null : $this->mapper->toDomainEntity($ormEntity);
    }
}
