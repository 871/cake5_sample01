<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Cake\Admin;

use App\Domain\Admin\AdminGrant\Entity\GrantPermission as DomainGrantPermission;
use App\Domain\Admin\AdminGrant\Entity\GrantRolePermission as DomainGrantRolePermission;
use App\Lib\UUID\UUID;
use App\Model\Entity\Grant\GrantPermission as OrmGrantPermission;
use App\Model\Entity\Grant\GrantRolePermission as OrmGrantRolePermission;
use App\Model\Table\Grant\GrantRolePermissionsTable;
use App\Security\Input\Cast;
use Cake\ORM\Locator\LocatorAwareTrait;

final class AdminGrantMapper
{
    use LocatorAwareTrait;

    public const ACCOUNT_TYPE = 'ADMIN';

    /**
     * @var \App\Model\Table\Grant\GrantRolePermissionsTable
     */
    private GrantRolePermissionsTable $rolePermissionsTable;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->rolePermissionsTable = $this->fetchTable(GrantRolePermissionsTable::class);
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
}
