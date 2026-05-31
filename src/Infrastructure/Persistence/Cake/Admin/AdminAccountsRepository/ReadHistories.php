<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Cake\Admin\AdminAccountsRepository;

use App\Domain\Admin\AdminAccounts\Entity\AdminAccountHistory as DomainHistoryEntity;
use App\Domain\Admin\AdminAccounts\ValueObject as Vo;
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
     * @var \App\Infrastructure\Persistence\Cake\Admin\AdminAccountsRepository\Mapper
     */
    private Mapper $mapper;

    /**
     * @param \App\Domain\Admin\AdminAccounts\ValueObject\Id $adminAccountId
     */
    public function __construct(
        private readonly Vo\Id $adminAccountId,
    ) {
        $this->table = $this->fetchTable(AdminAccountHistoriesTable::class);
        $this->mapper = new Mapper();
    }

    /**
     * @return array<\App\Domain\Admin\AdminAccounts\Entity\AdminAccountHistory>
     */
    public function run(): array
    {
        /** @var array<\App\Model\Entity\Admin\AdminAccountHistory> $results */
        $results = $this->table
            ->find()
            ->contain(['AccountStatusMasters'])
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
