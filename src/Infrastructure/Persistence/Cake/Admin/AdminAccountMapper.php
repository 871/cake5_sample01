<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Cake\Admin;

use App\Domain\Admin\AdminAccounts\Entity\AdminAccount as DomainEntity;
use App\Lib\UUID\UUID;
use App\Model\Entity\Admin\AdminAccount as OrmEntity;
use App\Model\Table\Admin\AdminAccountsTable;
use App\Security\Input\Cast;
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
        return $this->table->newEntity([
            'id' => UUID::uuid7(),
            'login_id' => $domainEntity->loginId()->toString(),
            'name' => $domainEntity->name()->toString(),
            'email' => $domainEntity->email()->toString(),
            'password' => $domainEntity->password()->toHash(),
            'role_id' => $domainEntity->roleId()->toString(),
            'status' => $domainEntity->status()->toInt(),
        ], [
            'validate' => false,
        ]);
    }

    /**
     * @param \App\Domain\Admin\AdminAccounts\Entity\AdminAccount $domainEntity
     * @param bool $changePassword
     * @return \App\Model\Entity\Admin\AdminAccount
     */
    public function toPatchOrmEntity(DomainEntity $domainEntity, bool $changePassword = false): OrmEntity
    {
        $ormEntity = $this->table->get($domainEntity->id()->toString());

        $data = [
            'login_id' => $domainEntity->loginId()->toString(),
            'name' => $domainEntity->name()->toString(),
            'email' => $domainEntity->email()->toString(),
            'role_id' => $domainEntity->roleId()->toString(),
            'status' => $domainEntity->status()->toInt(),
        ];

        if ($changePassword) {
            $data['password'] = $domainEntity->password()->toHash();
        }

        $this->table->patchEntity($ormEntity, $data, [
            'validate' => false,
        ]);

        return $ormEntity;
    }

    /**
     * @param \App\Model\Entity\Admin\AdminAccount $ormEntity
     * @return \App\Domain\Admin\AdminAccounts\Entity\AdminAccount
     */
    public function toDomainEntity(OrmEntity $ormEntity): DomainEntity
    {
        return new DomainEntity(
            id: $ormEntity->id,
            login_id: Cast::toString($ormEntity->login_id),
            name: Cast::toString($ormEntity->name),
            email: Cast::toString($ormEntity->email),
            password: null,
            role_id: Cast::toString($ormEntity->role_id),
            status: Cast::toString($ormEntity->status),
            role_name: Cast::toString($ormEntity->admin_role?->name),
        );
    }
}
