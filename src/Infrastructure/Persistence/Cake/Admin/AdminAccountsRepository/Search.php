<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Cake\Admin\AdminAccountsRepository;

use App\Domain\Admin\AdminAccounts\SearchCondition;
use App\Model\Table\Admin\AdminAccountsTable;
use Cake\ORM\Locator\LocatorAwareTrait;
use Cake\ORM\Query\SelectQuery;

final class Search
{
    use LocatorAwareTrait;

    /**
     * @var \App\Model\Table\Admin\AdminAccountsTable
     */
    private AdminAccountsTable $table;

    /**
     * @param \App\Domain\Admin\AdminAccounts\SearchCondition $condition
     */
    public function __construct(
        private readonly SearchCondition $condition,
    ) {
        $this->table = $this->fetchTable(AdminAccountsTable::class);
    }

    /**
     * @return \Cake\ORM\Query\SelectQuery<\App\Model\Entity\Admin\AdminAccount>
     */
    public function run(): SelectQuery
    {
        return $this->table
            ->find()
            ->contain(['AccountStatusMasters'])
            ->where(array_filter([
                'AdminAccounts.id' => $this->condition->getId()->toStringOrNull(),
                'AdminAccounts.account_status_master_id'
                    => $this->condition->getAccountStatusMasterId()->toStringOrNull(),
                'OR' => array_filter([
                    'AdminAccounts.email LIKE' => $this->condition->getKeyword()->toQueryLikeOrNull(),
                    'AdminAccounts.name LIKE' => $this->condition->getKeyword()->toQueryLikeOrNull(),
                ], fn($v) => !in_array($v, [null, '', []], true)),
            ], fn($v) => !in_array($v, [null, '', []], true)));
    }
}
