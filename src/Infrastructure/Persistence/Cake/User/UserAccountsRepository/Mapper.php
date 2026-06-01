<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Cake\User\UserAccountsRepository;

use App\Domain\Shared\ValueObject as SVo;
use App\Domain\User\UserAccounts\Entity\UserAccount as DomainEntity;
use App\Domain\User\UserAccounts\Entity\UserAccountHistory as DomainHistoryEntity;
use App\Domain\User\UserAccounts\ValueObject as Vo;
use App\Lib\UUID\UUID;
use App\Model\Entity\User\UserAccount as OrmEntity;
use App\Model\Entity\User\UserAccountHistory as OrmHistoryEntity;
use App\Model\Table\User\UserAccountHistoriesTable;
use App\Model\Table\User\UserAccountsTable;
use App\Security\Input\Cast;
use Authentication\PasswordHasher\DefaultPasswordHasher;
use Cake\ORM\Locator\LocatorAwareTrait;
use DateTimeInterface;

final class Mapper
{
    use LocatorAwareTrait;

    /**
     * @var \App\Model\Table\User\UserAccountsTable
     */
    private UserAccountsTable $table;

    /**
     * @var \App\Model\Table\User\UserAccountHistoriesTable
     */
    private UserAccountHistoriesTable $historyTable;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->table = $this->fetchTable(UserAccountsTable::class);
        $this->historyTable = $this->fetchTable(UserAccountHistoriesTable::class);
    }

    /**
     * @param \App\Domain\User\UserAccounts\Entity\UserAccount $domainEntity
     * @return \App\Model\Entity\User\UserAccount
     */
    public function toNewOrmEntity(DomainEntity $domainEntity): OrmEntity
    {
        $hasher = new DefaultPasswordHasher();

        return $this->table->newEntity([
            'email' => $domainEntity->email()->toString(),
            'password' => $hasher->hash($domainEntity->password()->toString()),
            'name' => $domainEntity->name()->toString(),
            'account_status_master_id' => $domainEntity->accountStatusMasterId()->toInt(),
            'is_email_verified' => $domainEntity->isEmailVerified()->toInt(),
            'password_changed_at' => $domainEntity->passwordChangedAt()->format('Y-m-d\TH:i:s'),
            'password_expires_at' => $domainEntity->passwordExpiresAt()->format('Y-m-d\TH:i:s'),
            'created' => $domainEntity->created()->format('Y-m-d\TH:i:s'),
            'created_by' => $domainEntity->createdBy()->toStringOrNull(),
            'created_ip' => $domainEntity->createdIp()->toStringOrNull(),
            'modified' => $domainEntity->modified()->format('Y-m-d\TH:i:s'),
            'modified_by' => $domainEntity->modifiedBy()->toStringOrNull(),
            'modified_ip' => $domainEntity->modifiedIp()->toStringOrNull(),
        ], [
            'validate' => false,
        ]);
    }

    /**
     * @param \App\Domain\User\UserAccounts\Entity\UserAccount $domainEntity
     * @return \App\Model\Entity\User\UserAccount
     */
    public function toPatchOrmEntity(DomainEntity $domainEntity): OrmEntity
    {
        /** @var \App\Model\Entity\User\UserAccount $ormEntity */
        $ormEntity = $this->table->get($domainEntity->id()->toInt());

        $this->table->patchEntity($ormEntity, [
            'email' => $domainEntity->email()->toString(),
            'password' => $domainEntity->password()->toString() === ''
                ? $ormEntity->password
                : (new DefaultPasswordHasher())->hash($domainEntity->password()->toString()),
            'name' => $domainEntity->name()->toString(),
            'account_status_master_id' => $domainEntity->accountStatusMasterId()->toInt(),
            'is_email_verified' => $domainEntity->isEmailVerified()->toInt(),
            'password_changed_at' => $domainEntity->passwordChangedAt()->format('Y-m-d\TH:i:s'),
            'password_expires_at' => $domainEntity->passwordExpiresAt()->format('Y-m-d\TH:i:s'),
            'modified' => $domainEntity->modified()->format('Y-m-d\TH:i:s'),
            'modified_by' => $domainEntity->modifiedBy()->toStringOrNull(),
            'modified_ip' => $domainEntity->modifiedIp()->toStringOrNull(),
        ], [
            'validate' => false,
        ]);

        return $ormEntity;
    }

    /**
     * @param \App\Model\Entity\User\UserAccount $ormEntity
     * @param string $operationType
     * @param \DateTimeInterface $historyCreated
     * @return \App\Model\Entity\User\UserAccountHistory
     */
    public function toNewOrmHistoryEntity(
        OrmEntity $ormEntity,
        string $operationType,
        DateTimeInterface $historyCreated,
    ): OrmHistoryEntity {
        return $this->historyTable->newEntity(array_merge($ormEntity->toArray(), [
            'id' => UUID::uuid7(),
            'user_account_id' => $ormEntity->id,
            'operation_type' => $operationType,
            'history_created' => $historyCreated->format('Y-m-d\TH:i:s'),
        ]), [
            'validate' => false,
        ]);
    }

    /**
     * @param \App\Model\Entity\User\UserAccount $ormEntity
     * @return \App\Domain\User\UserAccounts\Entity\UserAccount
     */
    public function toDomainEntity(OrmEntity $ormEntity): DomainEntity
    {
        return new DomainEntity(
            id: new Vo\Id(Cast::toStringOrNull($ormEntity->id)),
            email: Vo\Email::fromString(Cast::toStringOrNull($ormEntity->email)),
            password: Vo\Password::fromString(Cast::toStringOrNull($ormEntity->password)),
            name: Vo\Name::fromString(Cast::toStringOrNull($ormEntity->name)),
            account_status_master_id: new Vo\AccountStatusMasterId(
                Cast::toStringOrNull($ormEntity->account_status_master_id),
            ),
            account_status_master_code: new Vo\AccountStatusMasterCode(
                Cast::toStringOrNull($ormEntity->account_status_master->code),
            ),
            account_status_master_name: new Vo\AccountStatusMasterName(
                Cast::toStringOrNull($ormEntity->account_status_master->name),
            ),
            is_email_verified: new Vo\IsEmailVerified(Cast::toStringOrNull($ormEntity->is_email_verified)),
            password_changed_at: new Vo\PasswordChangedAt(
                Cast::toStringOrNull($ormEntity->password_changed_at->format('Y-m-d\TH:i:s')),
            ),
            password_expires_at: new Vo\PasswordExpiresAt(
                Cast::toStringOrNull($ormEntity->password_expires_at->format('Y-m-d\TH:i:s')),
            ),
            created: new SVo\Created(Cast::toStringOrNull($ormEntity->created->format('Y-m-d\TH:i:s'))),
            created_by: new SVo\CreatedBy(Cast::toStringOrNull($ormEntity->created_by)),
            created_ip: new SVo\CreatedIp(Cast::toStringOrNull($ormEntity->created_ip)),
            modified: new SVo\Modified(Cast::toStringOrNull($ormEntity->modified->format('Y-m-d\TH:i:s'))),
            modified_by: new SVo\ModifiedBy(Cast::toStringOrNull($ormEntity->modified_by)),
            modified_ip: new SVo\ModifiedIp(Cast::toStringOrNull($ormEntity->modified_ip)),
        );
    }

    /**
     * @param \App\Model\Entity\User\UserAccountHistory $ormEntity
     * @return \App\Domain\User\UserAccounts\Entity\UserAccountHistory
     */
    public function toDomainHistoryEntity(OrmHistoryEntity $ormEntity): DomainHistoryEntity
    {
        return new DomainHistoryEntity(
            id: new SVo\Uuid(Cast::toStringOrNull($ormEntity->id)),
            user_account_id: new Vo\Id(Cast::toStringOrNull($ormEntity->user_account_id)),
            email: Vo\Email::fromString(Cast::toStringOrNull($ormEntity->email)),
            password: Vo\Password::fromString(Cast::toStringOrNull($ormEntity->password)),
            name: Vo\Name::fromString(Cast::toStringOrNull($ormEntity->name)),
            account_status_master_id: new Vo\AccountStatusMasterId(
                Cast::toStringOrNull($ormEntity->account_status_master_id),
            ),
            account_status_master_code: new Vo\AccountStatusMasterCode(
                Cast::toStringOrNull($ormEntity->account_status_master->code),
            ),
            account_status_master_name: new Vo\AccountStatusMasterName(
                Cast::toStringOrNull($ormEntity->account_status_master->name),
            ),
            is_email_verified: new Vo\IsEmailVerified(Cast::toStringOrNull($ormEntity->is_email_verified)),
            password_changed_at: new Vo\PasswordChangedAt(
                Cast::toStringOrNull($ormEntity->password_changed_at->format('Y-m-d\TH:i:s')),
            ),
            password_expires_at: new Vo\PasswordExpiresAt(
                Cast::toStringOrNull($ormEntity->password_expires_at->format('Y-m-d\TH:i:s')),
            ),
            created: new SVo\Created(Cast::toStringOrNull($ormEntity->created->format('Y-m-d\TH:i:s'))),
            created_by: new SVo\CreatedBy(Cast::toStringOrNull($ormEntity->created_by)),
            created_ip: new SVo\CreatedIp(Cast::toStringOrNull($ormEntity->created_ip)),
            modified: new SVo\Modified(Cast::toStringOrNull($ormEntity->modified->format('Y-m-d\TH:i:s'))),
            modified_by: new SVo\ModifiedBy(Cast::toStringOrNull($ormEntity->modified_by)),
            modified_ip: new SVo\ModifiedIp(Cast::toStringOrNull($ormEntity->modified_ip)),
            operation_type: new SVo\OperationType(Cast::toStringOrNull($ormEntity->operation_type)),
            history_created: new SVo\HistoryCreated(
                Cast::toStringOrNull($ormEntity->history_created->format('Y-m-d\TH:i:s')),
            ),
        );
    }
}
