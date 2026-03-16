<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Cake\Admin;

use App\Domain\Admin\AdminAccounts\Entity\AdminAccount as DomainEntity;
use App\Model\Entity\Admin\AdminAccount as OrmEntity;
use App\Model\Entity\Admin\AdminAccountHistory as OrmHistoryEntity;
use App\Model\Table\Admin\AdminAccountsTable;
use App\Model\Table\Admin\AdminAccountHistoriesTable;
use App\Lib\UUID\UUID;
use App\Security\Input\Cast;
use Authentication\PasswordHasher\DefaultPasswordHasher;
use DateTimeImmutable;
use Cake\I18n\DateTime;
use Cake\ORM\Locator\LocatorAwareTrait;

final class AdminAccountMapper
{
    use LocatorAwareTrait;

    /**
     * @var \App\Model\Table\Admin\AdminAccountsTable
     */
    private AdminAccountsTable $table;

    /**
     * @var \App\Model\Table\Admin\AdminAccountHistoriesTable
     */
    private AdminAccountHistoriesTable $historyTable;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->table = $this->fetchTable(AdminAccountsTable::class);
        $this->historyTable = $this->fetchTable(AdminAccountHistoriesTable::class);
    }

    /**
     * @param \App\Domain\Admin\AdminAccounts\Entity\AdminAccount $domainEntity
     * @return \App\Model\Entity\Admin\AdminAccount
     */
    public function toNewOrmEntity(DomainEntity $domainEntity): OrmEntity
    {
        $hasher = new DefaultPasswordHasher();

        $entity = $this->table->newEntity([
            'email' => $domainEntity->email()->toString(),
            'password' => $hasher->hash($domainEntity->password()->toString()),
            'name' => $domainEntity->name()->toString(),
            'admin_note' => $domainEntity->adminNote()->toString() ?: null,
            'account_status_master_id' => $domainEntity->accountStatusMasterId()->toInt(),
            'is_email_verified' => $domainEntity->isEmailVerified()->toInt() ?? 0,
            'password_changed_at' => $domainEntity->passwordChangedAt()->format('Y-m-d\TH:i:s'),
            'password_expires_at' => $domainEntity->passwordExpiresAt()->format('Y-m-d\TH:i:s'),
            'created' => $domainEntity->created()->format('Y-m-d\TH:i:s'),
            'created_by' => $domainEntity->createdBy()->toString(),
            'created_ip' => $domainEntity->createdIp()->toString(),
            'modified' => $domainEntity->modified()->format('Y-m-d\TH:i:s'),
            'modified_by' => $domainEntity->modifiedBy()->toString(),
            'modified_ip' => $domainEntity->modifiedIp()->toString(),
        ], [
            'validate' => false,
        ]);

        return $entity;
    }

    /**
     * @param \App\Domain\Admin\AdminAccounts\Entity\AdminAccount $domainEntity
     * @return \App\Model\Entity\Admin\AdminAccount
     */
    public function toPatchOrmEntity(DomainEntity $domainEntity): OrmEntity
    {
        $ormEntity = $this->table->get($domainEntity->id()->toInt());

        $this->table->patchEntity($ormEntity, [
            'email' => $domainEntity->email()->toString(),
            'password' => $domainEntity->password()->toString() === ''
                ? $ormEntity->password
                : (new DefaultPasswordHasher())->hash($domainEntity->password()->toString()),
            'name' => $domainEntity->name()->toString(),
            'admin_note' => $domainEntity->adminNote()->toString() ?: null,
            'account_status_master_id' => $domainEntity->accountStatusMasterId()->toInt(),
            'is_email_verified' => $domainEntity->isEmailVerified()->toInt() ?? 0,
            'password_changed_at' => $domainEntity->passwordChangedAt()->format('Y-m-d\TH:i:s'),
            'password_expires_at' => $domainEntity->passwordExpiresAt()->format('Y-m-d\TH:i:s'),
            'modified' => $domainEntity->modified()->format('Y-m-d\TH:i:s'),
            'modified_by' => $domainEntity->modifiedBy()->toString(),
            'modified_ip' => $domainEntity->modifiedIp()->toString(),
        ], [
            'validate' => false,
        ]);

        return $ormEntity;
    }

    /**
     * @param \App\Model\Entity\Admin\AdminAccount $ormEntity
     * @param string $operationType
     * @param \DateTimeImmutable $history_created
     * @return \App\Model\Entity\Admin\AdminAccountHistory
     */
    public function toNewOrmHistoryEntity(
        OrmEntity $ormEntity,
        string $operationType,
        DateTimeImmutable $history_created
    ): OrmHistoryEntity {
        return $this->historyTable->newEntity(array_merge($ormEntity->toArray(), [
            'id' => UUID::uuid7(),
            'admin_account_id' => $ormEntity->id,
            'operation_type' => $operationType,
            'history_created' => $history_created->format('Y-m-d\TH:i:s'),
        ]), [
            'validate' => false,
        ]);
    }

    /**
     * @param \App\Model\Entity\Admin\AdminAccount $ormEntity
     * @return \App\Domain\Admin\AdminAccounts\Entity\AdminAccount
     */
    public function toDomainEntity(OrmEntity $ormEntity): DomainEntity
    {
        return new DomainEntity(
            id: Cast::toString($ormEntity->id),
            email: Cast::toString($ormEntity->email),
            password: Cast::toString($ormEntity->password),
            name: Cast::toString($ormEntity->name),
            admin_note: Cast::toString($ormEntity->admin_note),
            account_status_master_id: Cast::toString($ormEntity->account_status_master_id),
            is_email_verified: Cast::toString($ormEntity->is_email_verified),
            password_changed_at: Cast::toString($ormEntity->password_changed_at?->format('Y-m-d\TH:i:s')),
            password_expires_at: Cast::toString($ormEntity->password_expires_at?->format('Y-m-d\TH:i:s')),
            created: Cast::toString($ormEntity->created?->format('Y-m-d\TH:i:s')),
            created_by: Cast::toString($ormEntity->created_by),
            created_ip: Cast::toString($ormEntity->created_ip),
            modified: Cast::toString($ormEntity->modified?->format('Y-m-d\TH:i:s')),
            modified_by: Cast::toString($ormEntity->modified_by),
            modified_ip: Cast::toString($ormEntity->modified_ip),
        );
    }
}
