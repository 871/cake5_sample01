<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Cake\User\UserAccountsRepository;

use App\Domain\Shared\ValueObject as SVo;
use App\Domain\User\UserAccounts\Entity\UserAccount as DomainEntity;
use App\Domain\User\UserAccounts\ValueObject as Vo;
use App\Model\Table\User\UserAccountHistoriesTable;
use App\Model\Table\User\UserAccountsTable;
use Cake\ORM\Locator\LocatorAwareTrait;
use DateTimeInterface;

final class Delete
{
    use LocatorAwareTrait;

    /**
     * @var \App\Model\Table\User\UserAccountsTable
     */
    private UserAccountsTable $table;

    /**
     * @var \App\Model\Table\User\UserAccountHistoriesTable
     */
    private UserAccountHistoriesTable $historyTable;

    /**
     * @var \App\Infrastructure\Persistence\Cake\User\UserAccountsRepository\Mapper
     */
    private Mapper $mapper;

    /**
     * @param \App\Domain\User\UserAccounts\ValueObject\Id $id
     */
    public function __construct(
        private readonly Vo\Id $id,
        private readonly DateTimeInterface $datetime,
    ) {
        $this->table = $this->fetchTable(UserAccountsTable::class);
        $this->historyTable = $this->fetchTable(UserAccountHistoriesTable::class);
        $this->mapper = new Mapper();
    }

    /**
     * @return \App\Domain\User\UserAccounts\Entity\UserAccount
     */
    public function run(): DomainEntity
    {
        /** @var \App\Model\Entity\User\UserAccount $ormEntity */
        $ormEntity = $this->table->get($this->id->toInt());

        $this->table->getConnection()->transactional(function () use ($ormEntity): void {
            $this->historyTable->saveOrFail(
                $this->mapper->toNewOrmHistoryEntity(
                    $ormEntity,
                    SVo\OperationType::DELETE,
                    $this->datetime,
                ),
                [
                    'checkExisting' => false,
                ],
            );

            $this->table->deleteOrFail($ormEntity);
        });

        return $this->mapper->toDomainEntity($ormEntity);
    }
}
