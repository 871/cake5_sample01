<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Cake\Admin\AdminAccountsRepository;

use App\Domain\Admin\AdminAccounts\Entity\AdminAccountHistory as DomainHistoryEntity;
use App\Domain\Admin\AdminAccounts\ValueObject as Vo;
use App\Model\Entity\Admin\AdminAccountHistory as OrmHistoryEntity;
use App\Model\Table\Admin\AdminAccountHistoriesTable;
use App\Domain\Exception\RepositoryException;
use Cake\ORM\Locator\LocatorAwareTrait;
use Cake\ORM\Query;

final class ReadHistories
{
    use LocatorAwareTrait;

    /**
     * @var \App\Model\Table\Admin\AdminAccountHistoriesTable
     */
    private AdminAccountHistoriesTable $table;

    /**
     * @param \App\Domain\Admin\AdminAccounts\ValueObject\Id $adminAccountId
     */
    public function __construct(
        private readonly Vo\Id $adminAccountId,
    ) {
        $this->table = $this->fetchTable(AdminAccountHistoriesTable::class);
    }

    /**
     * @return array<\App\Domain\Admin\AdminAccounts\Entity\AdminAccountHistory>
     */
    public function run(): array
    {
        /** @var array<\App\Model\Entity\Admin\AdminAccountHistory> $ormEntities */
        $ormEntities = $this->table
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
                'AdminAccountHistories__operation_type' => 'AdminAccountHistories.operation_type',
                'AdminAccountHistories__history_created' => 'AdminAccountHistories.history_created',
            ])
            ->where([
                'AdminAccountHistories.admin_account_id' => $this->adminAccountId->toInt(),
            ])
            ->orderBy(['AdminAccountHistories.history_created' => 'DESC'])
            ->all()
            ->toList();

        return array_map(function (OrmHistoryEntity $entity) {
            return new DomainHistoryEntity(
                id: $entity->id,
                admin_account_id: $entity->admin_account_id === null
                    ? null : (string)$entity->admin_account_id,
                email: $entity->email,
                name: $entity->name,
                admin_note: $entity->admin_note,
                account_status_master_id: $entity->account_status_master_id === null
                    ? null : (string)$entity->account_status_master_id,
                is_email_verified: $entity->is_email_verified === null
                    ? null : (string)$entity->is_email_verified,
                password_changed_at: $entity->password_changed_at?->format('Y-m-d\TH:i:s'),
                password_expires_at: $entity->password_expires_at?->format('Y-m-d\TH:i:s'),
                operation_type: $entity->operation_type,
                history_created: $entity->history_created?->format('Y-m-d H:i:s'),
            );
        }, $ormEntities);
    }
}
