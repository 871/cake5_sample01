<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Cake\Admin\AdminAccountsRepository;

use App\Domain\Admin\AdminAccounts\Entity\AdminAccount as DomainEntity;
use App\Domain\Admin\AdminAccounts\ValueObject as Vo;
use App\Model\Entity\Admin\AdminAccount as OrmEntity;
use App\Infrastructure\Persistence\Cake\Admin\AdminAccountMapper;
use App\Model\Table\Admin\AdminAccountsTable;
use App\Domain\Exception\RepositoryException;
use Cake\ORM\Locator\LocatorAwareTrait;

final class Read
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
     * @param \App\Domain\Admin\AdminAccounts\ValueObject\Id $id
     */
    public function __construct(
        private readonly Vo\Id $id,
    ) {
        $this->table = $this->fetchTable(AdminAccountsTable::class);
        $this->mapper = new AdminAccountMapper();
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
                'AdminAccounts__email' => 'AdminAccounts.email',
                'AdminAccounts__password' => 'AdminAccounts.password',
                'AdminAccounts__name' => 'AdminAccounts.name',
                'AdminAccounts__admin_note' => 'AdminAccounts.admin_note',
                'AdminAccounts__account_status_master_id' => 'AdminAccounts.account_status_master_id',
                'AdminAccounts__is_email_verified' => 'AdminAccounts.is_email_verified',
                'AdminAccounts__password_changed_at' => 'AdminAccounts.password_changed_at',
                'AdminAccounts__password_expires_at' => 'AdminAccounts.password_expires_at',
            ])
            ->where([
                'AdminAccounts.id' => $this->id->toInt(),
            ])
            ->formatResults(function ($results) {
                return $results->map(function (OrmEntity $entity) {
                    return $this->mapper->toDomainEntity($entity);
                });
            })
            ->first() ?? throw new RepositoryException(
                'AdminAccount data not found'
                . '[id: ' . $this->id->toString() . ']',
            );
    }
}
