<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Cake\Admin\AdminAccountsRepository;

use App\Domain\Admin\AdminAccounts\Entity\AdminAccount as DomainEntity;
use App\Domain\Admin\AdminAccounts\ValueObject as Vo;
use App\Domain\Shared\ValueObject as SVo;
use App\Model\Table\Admin\AdminAccountHistoriesTable;
use App\Model\Table\Admin\AdminAccountsTable;
use Cake\ORM\Locator\LocatorAwareTrait;
use DateTimeInterface;

final class Delete
{
    use LocatorAwareTrait;

    /**
     * @var \App\Model\Table\Admin\AdminAccountsTable
     */
    private AdminAccountsTable $table;

    /**
     * @var \App\Model\Table\Admin\AdminAccountHistoriesTable
     */
    private AdminAccountHistoriesTable $historyTable;

    /**
     * @var \App\Infrastructure\Persistence\Cake\Admin\AdminAccountsRepository\Mapper
     */
    private Mapper $mapper;

    /**
     * @param \App\Domain\Admin\AdminAccounts\ValueObject\Id $id
     */
    public function __construct(
        private readonly Vo\Id $id,
        private readonly DateTimeInterface $datetime,
    ) {
        $this->table = $this->fetchTable(AdminAccountsTable::class);
        $this->historyTable = $this->fetchTable(AdminAccountHistoriesTable::class);
        $this->mapper = new Mapper();
    }

    /**
     * @return \App\Domain\Admin\AdminAccounts\Entity\AdminAccount
     */
    public function run(): DomainEntity
    {
        /** @var \App\Model\Entity\Admin\AdminAccount $ormEntity */
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
