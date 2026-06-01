<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Cake\User\UserAccountsRepository;

use App\Domain\User\UserAccounts\Entity\UserAccountHistory as DomainHistoryEntity;
use App\Domain\User\UserAccounts\ValueObject as Vo;
use App\Model\Entity\User\UserAccountHistory as OrmHistoryEntity;
use App\Model\Table\User\UserAccountHistoriesTable;
use Cake\ORM\Locator\LocatorAwareTrait;

final class ReadHistories
{
    use LocatorAwareTrait;

    /**
     * @var \App\Model\Table\User\UserAccountHistoriesTable
     */
    private UserAccountHistoriesTable $table;

    /**
     * @var \App\Infrastructure\Persistence\Cake\User\UserAccountsRepository\Mapper
     */
    private Mapper $mapper;

    /**
     * @param \App\Domain\User\UserAccounts\ValueObject\Id $userAccountId
     */
    public function __construct(
        private readonly Vo\Id $userAccountId,
    ) {
        $this->table = $this->fetchTable(UserAccountHistoriesTable::class);
        $this->mapper = new Mapper();
    }

    /**
     * @return array<\App\Domain\User\UserAccounts\Entity\UserAccountHistory>
     */
    public function run(): array
    {
        /** @var array<\App\Model\Entity\User\UserAccountHistory> $results */
        $results = $this->table
            ->find()
            ->contain(['AccountStatusMasters'])
            ->where([
                'UserAccountHistories.user_account_id' => $this->userAccountId->toInt(),
            ])
            ->orderBy(['UserAccountHistories.history_created' => 'DESC'])
            ->limit(100)
            ->all()
            ->toList();

        return array_map(
            fn(OrmHistoryEntity $entity): DomainHistoryEntity => $this->mapper->toDomainHistoryEntity($entity),
            $results,
        );
    }
}
