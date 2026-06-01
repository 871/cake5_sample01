<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Cake\User\UserAccountsRepository;

use App\Domain\Exception\RepositoryException;
use App\Domain\User\UserAccounts\Entity\UserAccount as DomainEntity;
use App\Domain\User\UserAccounts\ValueObject as Vo;
use App\Model\Table\User\UserAccountsTable;
use Cake\ORM\Locator\LocatorAwareTrait;

final class Read
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
     * @param \App\Domain\User\UserAccounts\ValueObject\Id $id
     */
    public function __construct(
        private readonly Vo\Id $id,
    ) {
        $this->table = $this->fetchTable(UserAccountsTable::class);
        $this->mapper = new Mapper();
    }

    /**
     * @return \App\Domain\User\UserAccounts\Entity\UserAccount
     */
    public function run(): DomainEntity
    {
        /** @var \App\Model\Entity\User\UserAccount $ormEntity */
        $ormEntity = $this->table
            ->find()
            ->contain(['AccountStatusMasters'])
            ->where([
                'UserAccounts.id' => $this->id->toInt(),
            ])
            ->first() ?? throw new RepositoryException(
                'UserAccount data not found'
                . '[id: ' . $this->id->toString() . ']',
            );

        return $this->mapper->toDomainEntity($ormEntity);
    }
}
