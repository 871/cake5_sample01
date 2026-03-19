<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Cake\Admin\AdminAccountsRepository;

use App\Domain\Admin\AdminAccounts\Entity\AdminAccountHistory as DomainHistoryEntity;
use App\Domain\Admin\AdminAccounts\ValueObject as Vo;
use App\Infrastructure\Persistence\Cake\Admin\AdminAccountMapper;
use App\Model\Entity\Admin\AdminAccountHistory as OrmHistoryEntity;
use App\Model\Table\Admin\AdminAccountHistoriesTable;
use Cake\ORM\Locator\LocatorAwareTrait;

final class ReadHistories
{
    use LocatorAwareTrait;

    /**
     * @var \App\Model\Table\Admin\AdminAccountHistoriesTable
     */
    private AdminAccountHistoriesTable $table;

    /**
     * @var \App\Infrastructure\Persistence\Cake\Admin\AdminAccountMapper
     */
    private AdminAccountMapper $mapper;

    /**
     * @param \App\Domain\Admin\AdminAccounts\ValueObject\Id $adminAccountId
     */
    public function __construct(
        private readonly Vo\Id $adminAccountId,
    ) {
        $this->table = $this->fetchTable(AdminAccountHistoriesTable::class);
        $this->mapper = new AdminAccountMapper();
    }

    /**
     * @return array<\App\Domain\Admin\AdminAccounts\Entity\AdminAccountHistory>
     */
    public function run(): array
    {
        /** @var array<\App\Model\Entity\Admin\AdminAccountHistory> $results */
        $results = $this->table
            ->find()
            ->select([
                'AdminAccountHistories__id' => 'AdminAccountHistories.id',
                'AdminAccountHistories__admin_account_id' => 'AdminAccountHistories.admin_account_id',
                'AdminAccountHistories__email' => 'AdminAccountHistories.email',
                'AdminAccountHistories__name' => 'AdminAccountHistories.name',
                'AdminAccountHistories__admin_note' => 'AdminAccountHistories.admin_note',
                'AdminAccountHistories__account_status_master_id' => 'AdminAccountHistories.account_status_master_id',
                'AdminAccountHistories__is_email_verified' => 'AdminAccountHistories.is_email_verified',
                'AdminAccountHistories__password_changed_at' => 'AdminAccountHistories.password_changed_at',
                'AdminAccountHistories__password_expires_at' => 'AdminAccountHistories.password_expires_at',
                'AdminAccountHistories__created' => 'AdminAccountHistories.created',
                'AdminAccountHistories__created_by' => 'AdminAccountHistories.created_by',
                'AdminAccountHistories__created_ip' => 'AdminAccountHistories.created_ip',
                'AdminAccountHistories__modified' => 'AdminAccountHistories.modified',
                'AdminAccountHistories__modified_by' => 'AdminAccountHistories.modified_by',
                'AdminAccountHistories__modified_ip' => 'AdminAccountHistories.modified_ip',
                'AdminAccountHistories__operation_type' => 'AdminAccountHistories.operation_type',
                'AdminAccountHistories__history_created' => 'AdminAccountHistories.history_created',
            ])
            ->where([
                'AdminAccountHistories.admin_account_id' => $this->adminAccountId->toInt(),
            ])
            ->orderBy(['AdminAccountHistories.history_created' => 'DESC'])
            ->limit(100)
            ->all()
            ->toList();

        return array_map(
            fn(OrmHistoryEntity $entity): DomainHistoryEntity => $this->mapper->toDomainHistoryEntity($entity),
            $results,
        );
    }
}
