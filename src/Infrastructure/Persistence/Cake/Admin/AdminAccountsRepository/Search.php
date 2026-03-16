<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Cake\Admin\AdminAccountsRepository;

use App\Domain\Admin\AdminAccounts\Entity\AdminAccount as DomainEntity;
use App\Domain\Admin\AdminAccounts\SearchCondition;
use App\Model\Entity\Admin\AdminAccount as OrmEntity;
use App\Model\Table\Admin\AdminAccountsTable;
use App\Security\Input\Cast;
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
                'AdminAccounts__login_id' => 'AdminAccounts.login_id',
                'AdminAccounts__name' => 'AdminAccounts.name',
                'AdminAccounts__email' => 'AdminAccounts.email',
                'AdminAccounts__role_id' => 'AdminAccounts.role_id',
                'AdminAccounts__status' => 'AdminAccounts.status',
                'AdminRoles__name' => 'AdminRoles.name',
            ])
            ->contain(['AdminRoles'])
            ->where(
                array_filter([
                    $this->condition->getRoleId()->toString() !== ''
                        ? ['AdminAccounts.role_id' => $this->condition->getRoleId()->toString()]
                        : null,
                    $this->condition->getStatus()->toString() !== ''
                        ? ['AdminAccounts.status' => $this->condition->getStatus()->toString()]
                        : null,
                ], fn($v) => $v !== null),
            );

        $keyword = $this->condition->getKeyword()->toString();
        if ($keyword !== '') {
            $query->where(function ($exp) use ($keyword) {
                return $exp->or([
                    'AdminAccounts.login_id LIKE' => '%' . $keyword . '%',
                    'AdminAccounts.name LIKE' => '%' . $keyword . '%',
                    'AdminAccounts.email LIKE' => '%' . $keyword . '%',
                ]);
            });
        }

        return $query->formatResults(function ($results) {
            return $results->map(function (OrmEntity $entity) {
                return new DomainEntity(
                    id: Cast::toString($entity->id),
                    login_id: Cast::toString($entity->login_id),
                    name: Cast::toString($entity->name),
                    email: Cast::toString($entity->email),
                    password: null,
                    role_id: Cast::toString($entity->role_id),
                    status: Cast::toString($entity->status),
                    role_name: Cast::toString($entity->admin_role?->name),
                );
            });
        });
    }
}
