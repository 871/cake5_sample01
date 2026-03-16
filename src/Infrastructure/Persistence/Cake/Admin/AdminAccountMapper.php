<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Cake\Admin;

use App\Domain\Admin\AdminAccounts\Entity\AdminAccount as DomainEntity;
use App\Model\Entity\Admin\AdminAccount as OrmEntity;
use App\Model\Table\Admin\AdminAccountsTable;
use App\Security\Input\Cast;
use Authentication\PasswordHasher\DefaultPasswordHasher;
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
     * Constructor.
     */
    public function __construct()
    {
        $this->table = $this->fetchTable(AdminAccountsTable::class);
    }

    /**
     * @param \App\Domain\Admin\AdminAccounts\Entity\AdminAccount $domainEntity
     * @return \App\Model\Entity\Admin\AdminAccount
     */
    public function toNewOrmEntity(DomainEntity $domainEntity): OrmEntity
    {
        $hasher = new DefaultPasswordHasher();
        $now = new DateTime();

        $entity = $this->table->newEntity([
            'email' => $domainEntity->email()->toString(),
            'password' => $hasher->hash($domainEntity->password()->toString()),
            'name' => $domainEntity->name()->toString(),
            'admin_note' => $domainEntity->adminNote()->toString() ?: null,
            'account_status_master_id' => $domainEntity->accountStatusMasterId()->toInt(),
            'is_email_verified' => $domainEntity->isEmailVerified()->toInt() ?? 0,
            'password_changed_at' => $domainEntity->passwordChangedAt()->format('Y-m-d H:i:s'),
            'password_expires_at' => $domainEntity->passwordExpiresAt()->format('Y-m-d H:i:s'),
        ], [
            'validate' => false,
        ]);

        $entity->created = $now;
        $entity->modified = $now;

        return $entity;
    }

    /**
     * @param \App\Domain\Admin\AdminAccounts\Entity\AdminAccount $domainEntity
     * @param string $currentHashedPassword
     * @return \App\Model\Entity\Admin\AdminAccount
     */
    public function toPatchOrmEntity(DomainEntity $domainEntity, string $currentHashedPassword): OrmEntity
    {
        $ormEntity = $this->table->get($domainEntity->id()->toInt());

        $newPassword = $domainEntity->password()->toString();
        $hashedPassword = ($newPassword !== '')
            ? (new DefaultPasswordHasher())->hash($newPassword)
            : $currentHashedPassword;

        $this->table->patchEntity($ormEntity, [
            'email' => $domainEntity->email()->toString(),
            'password' => $hashedPassword,
            'name' => $domainEntity->name()->toString(),
            'admin_note' => $domainEntity->adminNote()->toString() ?: null,
            'account_status_master_id' => $domainEntity->accountStatusMasterId()->toInt(),
            'is_email_verified' => $domainEntity->isEmailVerified()->toInt() ?? 0,
            'password_changed_at' => $domainEntity->passwordChangedAt()->format('Y-m-d H:i:s'),
            'password_expires_at' => $domainEntity->passwordExpiresAt()->format('Y-m-d H:i:s'),
        ], [
            'validate' => false,
        ]);

        $ormEntity->modified = new DateTime();

        return $ormEntity;
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
        );
    }
}
