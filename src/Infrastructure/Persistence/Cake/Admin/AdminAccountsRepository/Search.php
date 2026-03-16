<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Cake\Admin\AdminAccountsRepository;

use App\Domain\Admin\AdminAccounts\Entity\AdminAccount as DomainEntity;
use App\Domain\Admin\AdminAccounts\SearchCondition;
use App\Infrastructure\Persistence\Cake\Admin\AdminAccountMapper;
use App\Model\Entity\Admin\AdminAccount as OrmEntity;
use App\Model\Table\Admin\AdminAccountsTable;
use Cake\ORM\Locator\LocatorAwareTrait;
use Cake\ORM\Query;

final class Search
{
    use LocatorAwareTrait;

    /**
     * @var \App\Model\Table\Admin\AdminAccountsTable
     */
    private AdminAccountsTable $table;

    /**
     * @var \App\Infrastructure\Persistence\Cake\Admin\AdminAccountMapper
     */
    private AdminAccountMapper $mapper;

    /**
     * @param \App\Domain\Admin\AdminAccounts\SearchCondition $condition
     */
    public function __construct(
        private readonly SearchCondition $condition,
    ) {
        $this->table = $this->fetchTable(AdminAccountsTable::class);
        $this->mapper = new AdminAccountMapper();
    }

    /**
     * @return \Cake\ORM\Query
     */
    public function run(): Query
    {
        return $this->table
            ->find()
            ->select([
                'AdminAccounts__id' => 'AdminAccounts.id',
                'AdminAccounts__email' => 'AdminAccounts.email',
                'AdminAccounts__name' => 'AdminAccounts.name',
                'AdminAccounts__admin_note' => 'AdminAccounts.admin_note',
                'AdminAccounts__account_status_master_id' => 'AdminAccounts.account_status_master_id',
                'AdminAccounts__is_email_verified' => 'AdminAccounts.is_email_verified',
                'AdminAccounts__password_changed_at' => 'AdminAccounts.password_changed_at',
                'AdminAccounts__password_expires_at' => 'AdminAccounts.password_expires_at',
                'AdminAccounts__created' => 'AdminAccounts.created',
                'AdminAccounts__modified' => 'AdminAccounts.modified',
                'AccountStatusMasters__name' => 'AccountStatusMasters.name',
            ])
            ->contain(['AccountStatusMasters'])
            ->where(array_filter([
                'AdminAccounts.id' => $this->condition->getId()->toString(),
                'AdminAccounts.account_status_master_id' => $this->condition->getAccountStatusMasterId()->toString(),
                'OR' => array_filter([
                    'AdminAccounts.email LIKE' => $this->condition->getKeyword()->toQueryLike(),
                    'AdminAccounts.name LIKE' => $this->condition->getKeyword()->toQueryLike(),
                ], fn ($v) => !in_array($v, [null, '', []], true)),
            ], fn ($v) => !in_array($v, [null, '', []], true)))
            ->formatResults(function ($results) {
                return $results->map(function (OrmEntity $entity) {
                    return $this->mapper->toDomainEntity($entity);
                });
            });
    }
}
