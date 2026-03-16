<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Cake\Admin\AdminAccountsRepository;

use App\Domain\Admin\AdminAccounts\Entity\AdminAccount as DomainEntity;
use App\Domain\Admin\AdminAccounts\SearchCondition;
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
     * @param \App\Domain\Admin\AdminAccounts\SearchCondition $condition
     */
    public function __construct(
        private readonly SearchCondition $condition,
    ) {
        $this->table = $this->fetchTable(AdminAccountsTable::class);
    }

    /**
     * @return \Cake\ORM\Query
     */
    public function run(): Query
    {
        $query = $this->table
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
            ->where(['AdminAccounts.id !=' => 900000]);

        if ($this->condition->getId()->toString() !== '') {
            $query->andWhere(['AdminAccounts.id' => $this->condition->getId()->toString()]);
        }

        if ($this->condition->getAccountStatusMasterId()->toString() !== '') {
            $query->andWhere([
                'AdminAccounts.account_status_master_id' => $this->condition->getAccountStatusMasterId()->toString(),
            ]);
        }

        if ($this->condition->getKeyword()->toString() !== '') {
            $keyword = '%' . $this->condition->getKeyword()->toString() . '%';
            $query->andWhere(function ($exp) use ($keyword) {
                return $exp->or([
                    'AdminAccounts.email LIKE' => $keyword,
                    'AdminAccounts.name LIKE' => $keyword,
                ]);
            });
        }

        return $query->formatResults(function ($results) {
            return $results->map(function (OrmEntity $entity) {
                return new DomainEntity(
                    id: $entity->id === null ? null : (string)$entity->id,
                    email: $entity->email,
                    password: null,
                    name: $entity->name,
                    admin_note: $entity->admin_note,
                    account_status_master_id: $entity->account_status_master_id === null
                        ? null : (string)$entity->account_status_master_id,
                    is_email_verified: $entity->is_email_verified === null
                        ? null : (string)$entity->is_email_verified,
                    password_changed_at: $entity->password_changed_at?->format('Y-m-d\TH:i:s'),
                    password_expires_at: $entity->password_expires_at?->format('Y-m-d\TH:i:s'),
                );
            });
        });
    }
}
