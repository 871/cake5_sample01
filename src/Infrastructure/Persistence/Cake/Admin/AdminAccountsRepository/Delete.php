<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Cake\Admin\AdminAccountsRepository;

use App\Domain\Admin\AdminAccounts\Entity\AdminAccount as DomainEntity;
use App\Domain\Admin\AdminAccounts\ValueObject as Vo;
use App\Infrastructure\Persistence\Cake\Admin\AdminAccountMapper;
use App\Lib\UUID\UUID;
use App\Model\Entity\Admin\AdminAccount as OrmEntity;
use App\Model\Table\Admin\AdminAccountsTable;
use App\Model\Table\Admin\AdminAccountHistoriesTable;
use Cake\I18n\DateTime;
use Cake\ORM\Locator\LocatorAwareTrait;

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
        $this->historyTable = $this->fetchTable(AdminAccountHistoriesTable::class);
        $this->mapper = new AdminAccountMapper();
    }

    /**
     * @return \App\Domain\Admin\AdminAccounts\Entity\AdminAccount
     */
    public function run(): DomainEntity
    {
        /** @var \App\Model\Entity\Admin\AdminAccount $ormEntity */
        $ormEntity = $this->table->get($this->id->toInt());

        $this->table->getConnection()->transactional(function () use ($ormEntity): void {
            $this->saveHistory($ormEntity, 'DELETE');
            $this->table->deleteOrFail($ormEntity);
        });

        return $this->mapper->toDomainEntity($ormEntity);
    }

    /**
     * @param \App\Model\Entity\Admin\AdminAccount $ormEntity
     * @param string $operationType
     * @return void
     */
    private function saveHistory(OrmEntity $ormEntity, string $operationType): void
    {
        $now = new DateTime();
        $historyEntity = $this->historyTable->newEntity([
            'id' => UUID::uuid7(),
            'admin_account_id' => $ormEntity->id,
            'email' => $ormEntity->email,
            'password' => $ormEntity->password,
            'name' => $ormEntity->name,
            'admin_note' => $ormEntity->admin_note,
            'account_status_master_id' => $ormEntity->account_status_master_id,
            'is_email_verified' => $ormEntity->is_email_verified,
            'password_changed_at' => $ormEntity->password_changed_at,
            'password_expires_at' => $ormEntity->password_expires_at,
            'operation_type' => $operationType,
            'history_created' => $now,
        ], [
            'validate' => false,
        ]);

        $historyEntity->created = $now;
        $historyEntity->modified = $now;

        $this->historyTable->saveOrFail($historyEntity, ['checkExisting' => false]);
    }
}
