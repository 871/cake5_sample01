<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Cake\Admin\AdminAccountsRepository;

use App\Domain\Admin\AdminAccounts\Entity\AdminAccount as DomainEntity;
use App\Domain\Admin\AdminAccounts\ValueObject as Vo;
use App\Model\Entity\Admin\AdminAccount as OrmEntity;
use App\Model\Table\Admin\AdminAccountsTable;
use App\Security\Input\Cast;
use Cake\ORM\Locator\LocatorAwareTrait;
use App\Domain\Exception\RepositoryException;

final class Read
{
    use LocatorAwareTrait;

    /**
     * @var \App\Model\Table\Admin\AdminAccountsTable
     */
    private AdminAccountsTable $table;

    /**
     * @param \App\Domain\Admin\AdminAccounts\ValueObject\Id $id
     */
    public function __construct(
        private readonly Vo\Id $id,
    ) {
        $this->table = $this->fetchTable(AdminAccountsTable::class);
    }

    /**
     * @return \App\Domain\Admin\AdminAccounts\Entity\AdminAccount
     */
    public function run(): DomainEntity
    {
        /** @var \App\Domain\Admin\AdminAccounts\Entity\AdminAccount */
        return $this->table
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
            ->where([
                'AdminAccounts.id' => $this->id->toString(),
            ])
            ->formatResults(function ($results) {
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
            })
            ->first() ?? throw new RepositoryException(
                'AdminAccount data not found'
                . '[id: ' . $this->id->toString() . ']',
            );
    }
}
