<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Cake\User\UserAccountsRepository;

use App\Domain\User\UserAccounts\SearchCondition;
use App\Model\Table\User\UserAccountsTable;
use Cake\ORM\Locator\LocatorAwareTrait;
use Cake\ORM\Query\SelectQuery;

final class Search
{
    use LocatorAwareTrait;

    /**
     * @var \App\Model\Table\User\UserAccountsTable
     */
    private UserAccountsTable $table;

    /**
     * @param \App\Domain\User\UserAccounts\SearchCondition $condition
     */
    public function __construct(
        private readonly SearchCondition $condition,
    ) {
        $this->table = $this->fetchTable(UserAccountsTable::class);
    }

    /**
     * @return \Cake\ORM\Query\SelectQuery<\App\Model\Entity\User\UserAccount>
     */
    public function run(): array
    {
        return $this->table
            ->find()
            ->contain(['AccountStatusMasters'])
            ->where(array_filter([
                'UserAccounts.id' => $this->condition->getId()->toIntOrNull(),
                'UserAccounts.account_status_master_id'
                    => $this->condition->getAccountStatusMasterId()->toIntOrNull(),
                'OR' => array_filter([
                    'UserAccounts.email LIKE' => $this->condition->getKeyword()->toQueryLikeOrNull(),
                    'UserAccounts.name LIKE' => $this->condition->getKeyword()->toQueryLikeOrNull(),
                ], fn($v) => !in_array($v, [null, '', []], true)),
            ], fn($v) => !in_array($v, [null, '', []], true)));
    }
}
