<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Cake\User\UserAccountsRepository;

use App\Domain\Exception\RepositoryException;
use App\Domain\Shared\ValueObject as SVo;
use App\Domain\User\UserAccounts\Entity\UserAccount as DomainEntity;
use App\Model\Entity\User\UserAccount as OrmEntity;
use App\Model\Table\User\UserAccountHistoriesTable;
use App\Model\Table\User\UserAccountsTable;
use Cake\ORM\Exception\PersistenceFailedException;
use Cake\ORM\Locator\LocatorAwareTrait;

final class Create
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
     * @param \App\Domain\User\UserAccounts\Entity\UserAccount $domainEntity
     */
    public function __construct(
        private readonly DomainEntity $domainEntity,
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
        try {
            /** @var \App\Model\Entity\User\UserAccount $ormEntity */
            $ormEntity = $this->table->getConnection()->transactional(
                function (): OrmEntity {
                    $savedEntity = $this->table->saveOrFail(
                        $this->mapper->toNewOrmEntity($this->domainEntity),
                        [
                            'checkExisting' => false,
                        ],
                    );

                    $this->historyTable->saveOrFail(
                        $this->mapper->toNewOrmHistoryEntity(
                            $savedEntity,
                            SVo\OperationType::INSERT,
                            $savedEntity->created,
                        ),
                        [
                            'checkExisting' => false,
                        ],
                    );

                    return $savedEntity;
                },
            );

            return $this->mapper->toDomainEntity($ormEntity);
        } catch (PersistenceFailedException $ex) {
            throw new RepositoryException(
                message: 'UserAccountsRepository Create Error',
                previous: $ex,
            );
        }
    }
}
