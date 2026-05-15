<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Cake\Admin;

use App\Domain\Admin\AdminGrant\Entity\GrantAccountPermission as DomainGrantAccountPermission;
use App\Domain\Admin\AdminGrant\Entity\GrantAccountRole as DomainGrantAccountRole;
use App\Domain\Admin\AdminGrant\Entity\GrantPermission as DomainGrantPermission;
use App\Domain\Admin\AdminGrant\Entity\GrantRole as DomainGrantRole;
use App\Domain\Admin\AdminGrant\Entity\GrantRolePermission as DomainGrantRolePermission;
use App\Lib\UUID\UUID;
use App\Model\Entity\Grant\GrantAccountPermission as OrmGrantAccountPermission;
use App\Model\Entity\Grant\GrantAccountRole as OrmGrantAccountRole;
use App\Model\Entity\Grant\GrantPermission as OrmGrantPermission;
use App\Model\Entity\Grant\GrantRole as OrmGrantRole;
use App\Model\Entity\Grant\GrantRolePermission as OrmGrantRolePermission;
use App\Model\Table\Grant\GrantAccountPermissionsTable;
use App\Model\Table\Grant\GrantAccountRolesTable;
use App\Model\Table\Grant\GrantRolePermissionsTable;
use App\Security\Input\Cast;
use Cake\ORM\Locator\LocatorAwareTrait;

final class AdminGrantMapper
{
    use LocatorAwareTrait;

    public const ACCOUNT_TYPE = 'ADMIN';

    /**
     * @var \App\Model\Table\Grant\GrantAccountRolesTable
     */
    private GrantAccountRolesTable $accountRolesTable;

    /**
     * @var \App\Model\Table\Grant\GrantRolePermissionsTable
     */
    private GrantRolePermissionsTable $rolePermissionsTable;

    /**
     * @var \App\Model\Table\Grant\GrantAccountPermissionsTable
     */
    private GrantAccountPermissionsTable $accountPermissionsTable;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->accountRolesTable = $this->fetchTable(GrantAccountRolesTable::class);
        $this->rolePermissionsTable = $this->fetchTable(GrantRolePermissionsTable::class);
        $this->accountPermissionsTable = $this->fetchTable(GrantAccountPermissionsTable::class);
    }

    /**
     * @param \App\Domain\Admin\AdminGrant\Entity\GrantRolePermission $domainEntity
     * @return \App\Model\Entity\Grant\GrantRolePermission
     */
    public function toNewOrmRolePermissionEntity(DomainGrantRolePermission $domainEntity): OrmGrantRolePermission
    {
        return $this->rolePermissionsTable->newEntity([
            'id' => UUID::uuid7(),
            'account_type' => self::ACCOUNT_TYPE,
            'grant_role_id' => $domainEntity->grantRoleId()->toInt(),
            'grant_permission_id' => $domainEntity->grantPermissionId()->toInt(),
            'created' => $domainEntity->created()->format('Y-m-d\TH:i:s'),
            'modified' => $domainEntity->modified()->format('Y-m-d\TH:i:s'),
        ], [
            'validate' => false,
        ]);
    }

    /**
     * @param \App\Model\Entity\Grant\GrantRolePermission $ormEntity
     * @return \App\Domain\Admin\AdminGrant\Entity\GrantRolePermission
     */
    public function toDomainRolePermissionEntity(OrmGrantRolePermission $ormEntity): DomainGrantRolePermission
    {
        return new DomainGrantRolePermission(
            id: Cast::toStringOrNull($ormEntity->id),
            grant_role_id: Cast::toStringOrNull($ormEntity->grant_role_id),
            grant_permission_id: Cast::toStringOrNull($ormEntity->grant_permission_id),
            created: Cast::toStringOrNull($ormEntity->created->format('Y-m-d\TH:i:s')),
            modified: Cast::toStringOrNull($ormEntity->modified->format('Y-m-d\TH:i:s')),
        );
    }

    /**
     * @param \App\Model\Entity\Grant\GrantRole $ormEntity
     * @return \App\Domain\Admin\AdminGrant\Entity\GrantRole
     */
    public function toDomainRoleEntity(OrmGrantRole $ormEntity): DomainGrantRole
    {
        return new DomainGrantRole(
            id: Cast::toStringOrNull($ormEntity->id),
            code: Cast::toStringOrNull($ormEntity->code),
            name: Cast::toStringOrNull($ormEntity->name),
            description: Cast::toStringOrNull($ormEntity->description),
            sort: Cast::toStringOrNull($ormEntity->sort),
            is_active: Cast::toStringOrNull($ormEntity->is_active),
            created: Cast::toStringOrNull($ormEntity->created->format('Y-m-d\TH:i:s')),
            modified: Cast::toStringOrNull($ormEntity->modified->format('Y-m-d\TH:i:s')),
        );
    }

    /**
     * @param \App\Model\Entity\Grant\GrantPermission $ormEntity
     * @return \App\Domain\Admin\AdminGrant\Entity\GrantPermission
     */
    public function toDomainPermissionEntity(OrmGrantPermission $ormEntity): DomainGrantPermission
    {
        return new DomainGrantPermission(
            id: Cast::toStringOrNull($ormEntity->id),
            code: Cast::toStringOrNull($ormEntity->code),
            name: Cast::toStringOrNull($ormEntity->name),
            description: Cast::toStringOrNull($ormEntity->description),
            sort: Cast::toStringOrNull($ormEntity->sort),
            is_active: Cast::toStringOrNull($ormEntity->is_active),
            created: Cast::toStringOrNull($ormEntity->created->format('Y-m-d\TH:i:s')),
            modified: Cast::toStringOrNull($ormEntity->modified->format('Y-m-d\TH:i:s')),
        );
    }

    /**
     * @param int $accountId
     * @param int $grantRoleId
     * @param string $datetime
     * @return \App\Model\Entity\Grant\GrantAccountRole
     */
    public function toNewOrmAccountRoleEntity(
        int $accountId,
        int $grantRoleId,
        string $datetime,
    ): OrmGrantAccountRole {
        return $this->accountRolesTable->newEntity([
            'id' => UUID::uuid7(),
            'account_type' => self::ACCOUNT_TYPE,
            'account_id' => $accountId,
            'grant_role_id' => $grantRoleId,
            'created' => $datetime,
            'modified' => $datetime,
        ], [
            'validate' => false,
        ]);
    }

    /**
     * @param \App\Model\Entity\Grant\GrantAccountRole $ormEntity
     * @return \App\Domain\Admin\AdminGrant\Entity\GrantAccountRole
     */
    public function toDomainAccountRoleEntity(OrmGrantAccountRole $ormEntity): DomainGrantAccountRole
    {
        return new DomainGrantAccountRole(
            id: Cast::toStringOrNull($ormEntity->id),
            account_id: Cast::toStringOrNull($ormEntity->account_id),
            grant_role_id: Cast::toStringOrNull($ormEntity->grant_role_id),
            created: Cast::toStringOrNull($ormEntity->created->format('Y-m-d\TH:i:s')),
            modified: Cast::toStringOrNull($ormEntity->modified->format('Y-m-d\TH:i:s')),
        );
    }

    /**
     * @param int $accountId
     * @param int $grantPermissionId
     * @param string $datetime
     * @return \App\Model\Entity\Grant\GrantAccountPermission
     */
    public function toNewOrmAccountPermissionEntity(
        int $accountId,
        int $grantPermissionId,
        string $datetime,
    ): OrmGrantAccountPermission {
        return $this->accountPermissionsTable->newEntity([
            'id' => UUID::uuid7(),
            'account_type' => self::ACCOUNT_TYPE,
            'account_id' => $accountId,
            'grant_permission_id' => $grantPermissionId,
            'created' => $datetime,
            'modified' => $datetime,
        ], [
            'validate' => false,
        ]);
    }

    /**
     * @param \App\Model\Entity\Grant\GrantAccountPermission $ormEntity
     * @return \App\Domain\Admin\AdminGrant\Entity\GrantAccountPermission
     */
    public function toDomainAccountPermissionEntity(OrmGrantAccountPermission $ormEntity): DomainGrantAccountPermission
    {
        return new DomainGrantAccountPermission(
            id: Cast::toStringOrNull($ormEntity->id),
            account_id: Cast::toStringOrNull($ormEntity->account_id),
            grant_permission_id: Cast::toStringOrNull($ormEntity->grant_permission_id),
            created: Cast::toStringOrNull($ormEntity->created->format('Y-m-d\TH:i:s')),
            modified: Cast::toStringOrNull($ormEntity->modified->format('Y-m-d\TH:i:s')),
        );
    }
}
