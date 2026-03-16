<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Cake\Admin\AdminAccountsRepository;

use App\Domain\Admin\AdminAccounts\Entity\AdminAccount as DomainEntity;
use App\Domain\Exception\RepositoryException;
use App\Infrastructure\Persistence\Cake\Admin\AdminAccountMapper;
use App\Model\Entity\Admin\AdminAccount as OrmEntity;
use App\Model\Table\Admin\AdminAccountsTable;
use App\Model\Table\Admin\AdminAccountHistoriesTable;
use App\Domain\Shared\ValueObject as SVo;
use Cake\ORM\Exception\PersistenceFailedException;
use Cake\ORM\Locator\LocatorAwareTrait;

final class Update
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
     * @var \App\Infrastructure\Persistence\Cake\Admin\AdminAccountMapper
     */
    private AdminAccountMapper $mapper;

    /**
     * @param \App\Domain\Admin\AdminAccounts\Entity\AdminAccount $domainEntity
     */
    public function __construct(
        private readonly DomainEntity $domainEntity,
    ) {
        $this->table = $this->fetchTable(AdminAccountsTable::class);
        $this->historyTable = $this->fetchTable(AdminAccountHistoriesTable::class);
        $this->mapper = new AdminAccountMapper();
    }

    /**
     * @return \App\Domain\Admin\AdminAccounts\Entity\AdminAccount
     */
    public function run(): DomainEntity
    {
        try {
            // 現在のパスワードハッシュを取得する
            $currentOrmEntity = $this->table->get($this->domainEntity->id()->toInt());
            $currentHashedPassword = $currentOrmEntity->password;

            /** @var \App\Model\Entity\Admin\AdminAccount $ormEntity */
            $ormEntity = $this->table->getConnection()->transactional(
                function () use ($currentHashedPassword): OrmEntity {
                    $savedEntity = $this->table->saveOrFail(
                        $this->mapper->toPatchOrmEntity($this->domainEntity, $currentHashedPassword),
                        [
                            'checkExisting' => false,
                        ],
                    );

                    $this->historyTable->saveOrFail(
                        $this->mapper->toNewOrmHistoryEntity(
                            $savedEntity, 
                            SVo\OperationType::UPDATE,
                            $savedEntity->modified,
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
                message: 'AdminAccountsRepository Update Error',
                previous: $ex,
            );
        }
    }
}
