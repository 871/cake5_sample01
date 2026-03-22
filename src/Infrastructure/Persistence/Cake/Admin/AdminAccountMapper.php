<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Cake\Admin;

use App\Domain\Admin\AdminAccounts\Entity\AdminAccount as DomainEntity;
use App\Domain\Admin\AdminAccounts\Entity\AdminAccountHistory as DomainHistoryEntity;
use App\Lib\UUID\UUID;
use App\Model\Entity\Admin\AdminAccount as OrmEntity;
use App\Model\Entity\Admin\AdminAccountHistory as OrmHistoryEntity;
use App\Model\Table\Admin\AdminAccountHistoriesTable;
use App\Model\Table\Admin\AdminAccountsTable;
use App\Security\Input\Cast;
use Authentication\PasswordHasher\DefaultPasswordHasher;
use Cake\ORM\Locator\LocatorAwareTrait;
use DateTimeInterface;

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
            'is_email_verified' => $domainEntity->isEmailVerified()->toInt(),
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
        /** @var \App\Model\Entity\Admin\AdminAccount $ormEntity */
        $ormEntity = $this->table->get($domainEntity->id()->toInt());

        $this->table->patchEntity($ormEntity, [
            'email' => $domainEntity->email()->toString(),
            'password' => $domainEntity->password()->toString() === ''
                ? $ormEntity->password
                : (new DefaultPasswordHasher())->hash($domainEntity->password()->toString()),
            'name' => $domainEntity->name()->toString(),
            'admin_note' => $domainEntity->adminNote()->toString() ?: null,
            'account_status_master_id' => $domainEntity->accountStatusMasterId()->toInt(),
            'is_email_verified' => $domainEntity->isEmailVerified()->toInt(),
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
     * @param \DateTimeInterface $history_created
     * @return \App\Model\Entity\Admin\AdminAccountHistory
     */
    public function toNewOrmHistoryEntity(
        OrmEntity $ormEntity,
        string $operationType,
        DateTimeInterface $history_created,
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
            id: Cast::toStringOrNull($ormEntity->id),
            email: Cast::toStringOrNull($ormEntity->email),
            password: Cast::toStringOrNull($ormEntity->password),
            name: Cast::toStringOrNull($ormEntity->name),
            admin_note: Cast::toStringOrNull($ormEntity->admin_note),
            account_status_master_id: Cast::toStringOrNull($ormEntity->account_status_master_id),
            account_status_master_code: Cast::toStringOrNull($ormEntity->account_status_master->code),
            account_status_master_name: Cast::toStringOrNull($ormEntity->account_status_master->name),
            is_email_verified: Cast::toStringOrNull($ormEntity->is_email_verified),
            password_changed_at: Cast::toStringOrNull($ormEntity->password_changed_at->format('Y-m-d\TH:i:s')),
            password_expires_at: Cast::toStringOrNull($ormEntity->password_expires_at->format('Y-m-d\TH:i:s')),
            created: Cast::toStringOrNull($ormEntity->created->format('Y-m-d\TH:i:s')),
            created_by: Cast::toStringOrNull($ormEntity->created_by),
            created_ip: Cast::toStringOrNull($ormEntity->created_ip),
            modified: Cast::toStringOrNull($ormEntity->modified->format('Y-m-d\TH:i:s')),
            modified_by: Cast::toStringOrNull($ormEntity->modified_by),
            modified_ip: Cast::toStringOrNull($ormEntity->modified_ip),
        );
    }

    /**
     * @param \App\Model\Entity\Admin\AdminAccountHistory $ormEntity
     * @return \App\Domain\Admin\AdminAccounts\Entity\AdminAccountHistory
     */
    public function toDomainHistoryEntity(OrmHistoryEntity $ormEntity): DomainHistoryEntity
    {
        return new DomainHistoryEntity(
            id: Cast::toStringOrNull($ormEntity->id),
            admin_account_id: Cast::toStringOrNull($ormEntity->admin_account_id),
            email: Cast::toStringOrNull($ormEntity->email),
            name: Cast::toStringOrNull($ormEntity->name),
            admin_note: Cast::toStringOrNull($ormEntity->admin_note),
            account_status_master_id: Cast::toStringOrNull($ormEntity->account_status_master_id),
            account_status_master_code: Cast::toStringOrNull($ormEntity->account_status_master->code),
            account_status_master_name: Cast::toStringOrNull($ormEntity->account_status_master->name),
            is_email_verified: Cast::toStringOrNull($ormEntity->is_email_verified),
            password_changed_at: Cast::toStringOrNull($ormEntity->password_changed_at->format('Y-m-d\TH:i:s')),
            password_expires_at: Cast::toStringOrNull($ormEntity->password_expires_at->format('Y-m-d\TH:i:s')),
            created: Cast::toStringOrNull($ormEntity->created->format('Y-m-d\TH:i:s')),
            created_by: Cast::toStringOrNull($ormEntity->created_by),
            created_ip: Cast::toStringOrNull($ormEntity->created_ip),
            modified: Cast::toStringOrNull($ormEntity->modified->format('Y-m-d\TH:i:s')),
            modified_by: Cast::toStringOrNull($ormEntity->modified_by),
            modified_ip: Cast::toStringOrNull($ormEntity->modified_ip),
            operation_type: Cast::toStringOrNull($ormEntity->operation_type),
            history_created: Cast::toStringOrNull($ormEntity->history_created->format('Y-m-d\TH:i:s')),
        );
    }

    /**
     * @param \App\Domain\Admin\AdminAccounts\Entity\AdminAccount $domainEntity
     * @param \DateTimeInterface $nowDatetime
     * @return array<string, string|null>
     */
    public function toAuthSessionParams(
        DomainEntity $domainEntity,
        DateTimeInterface $nowDatetime,
    ): array {
        return [
            'account_id' => $domainEntity->id()->toString(),
            'account_email' => $domainEntity->email()->toString(),
            'account_name' => $domainEntity->name()->toString(),
            'account_status_master_id' => $domainEntity->accountStatusMasterId()->toString(),
            'account_status_master_code' => $domainEntity->accountStatusMasterCode()->toString(),
            'account_status_master_name' => $domainEntity->accountStatusMasterName()->toString(),
            'is_email_verified' => $domainEntity->isEmailVerified()->toString(),
            'password_changed_at' => $domainEntity->passwordChangedAt()->format('Y-m-d\TH:i:s'),
            'password_expires_at' => $domainEntity->passwordExpiresAt()->format('Y-m-d\TH:i:s'),
            'created' => $domainEntity->created()->format('Y-m-d\TH:i:s'),
            'created_by' => $domainEntity->createdBy()->toString(),
            'created_ip' => $domainEntity->createdIp()->toString(),
            'modified' => $domainEntity->modified()->format('Y-m-d\TH:i:s'),
            'modified_by' => $domainEntity->modifiedBy()->toString(),
            'modified_ip' => $domainEntity->modifiedIp()->toString(),
            'logined' => $nowDatetime->format('Y-m-d\TH:i:s'),
        ];
    }
}
