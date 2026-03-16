<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Cake\Admin\AdminAccountsRepository;

use App\Domain\Admin\AdminAccounts\Entity\AdminAccount as DomainEntity;
use App\Domain\Exception\RepositoryException;
use App\Infrastructure\Persistence\Cake\Admin\AdminAccountMapper;
use App\Model\Entity\Admin\AdminAccount as OrmEntity;
use App\Model\Table\Admin\AdminAccountsTable;
use App\Model\Table\Admin\AdminAccountHistoriesTable;
use App\Lib\UUID\UUID;
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
     * @param bool $changePassword
     */
    public function __construct(
        private readonly DomainEntity $domainEntity,
        private readonly bool $changePassword = false,
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
            /** @var \App\Model\Entity\Admin\AdminAccount $ormEntity */
            $ormEntity = $this->table->getConnection()->transactional(
                function (): OrmEntity {
                    $ormEntity = $this->table->saveOrFail(
                        $this->mapper->toPatchOrmEntity($this->domainEntity, $this->changePassword),
                        [
                            'checkExisting' => false,
                        ],
                    );

                    // 履歴保存
                    $this->historyTable->saveOrFail(
                        $this->historyTable->newEntity([
                            'id' => UUID::uuid7(),
                            'admin_account_id' => $ormEntity->id,
                            'login_id' => $ormEntity->login_id,
                            'name' => $ormEntity->name,
                            'email' => $ormEntity->email,
                            'role_id' => $ormEntity->role_id,
                            'status' => $ormEntity->status,
                            'operated_at' => date('Y-m-d H:i:s'),
                        ], ['validate' => false]),
                    );

                    return $ormEntity;
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
