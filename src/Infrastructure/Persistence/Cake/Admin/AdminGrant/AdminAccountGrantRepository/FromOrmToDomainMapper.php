<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Cake\Admin\AdminGrant\AdminAccountGrantRepository;

use App\Model\Entity\Admin\AdminAccount as OrmEntityAdminAccount;
use App\Model\Entity\Grant\GrantAccountRole as OrmEntityGrantAccountRole;
use App\Model\Entity\Grant\GrantRole as OrmEntityGrantRole;
use App\Model\Entity\Grant\GrantRolePermission as OrmEntityGrantRolePermission;
use App\Model\Entity\Grant\GrantAccountPermission as OrmEntityGrantAccountPermission;
use App\Model\Entity\Grant\GrantPermission as OrmEntityGrantPermission;
use App\Model\Entity\Shared\AccountStatusMaster as OrmAccountStatusMaster;
use App\Domain\Admin\AdminGrant\Entity\AdminAccountGrant as DomainEntityAdminAccountGrant;
use App\Domain\Admin\AdminGrant\Entity\GrantAccountRole as DomainEntityGrantAccountRole;
use App\Domain\Admin\AdminGrant\Entity\GrantRole as DomainEntityGrantRole;
use App\Domain\Admin\AdminGrant\Entity\GrantRolePermission as DomainEntityGrantRolePermission;
use App\Domain\Admin\AdminGrant\Entity\GrantAccountPermission  as DomainEntityGrantAccountPermission;
use App\Domain\Admin\AdminGrant\Entity\GrantPermission  as DomainEntityGrantPermission;
use App\Domain\Admin\AdminGrant\Entity\AccountStatusMaster as DomainAccountStatusMaster;
use App\Domain\Admin\AdminGrant\ValueObject as Vo;
use App\Domain\Shared\ValueObject as SVo;

final class FromOrmToDomainMapper
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        // 処理なし
    }

    /**
     * @param \App\Model\Entity\Admin\AdminAccount $ormEntity
     * @return \App\Domain\Admin\AdminGrant\Entity\AdminAccountGrant
     */
    public static function toAdminAccountGrant(OrmEntityAdminAccount $ormEntity): DomainEntityAdminAccountGrant
    {
        $domainEntityAdminAccountGrant = new DomainEntityAdminAccountGrant(
            admin_account_id: new Vo\AdminAccountId((string)$ormEntity->id),
            email: new Vo\Email((string)$ormEntity->email),
            name: new Vo\Name((string)$ormEntity->name),
            admin_note: new Vo\AdminNote((string)$ormEntity->admin_note),
            account_status_master_id: new Vo\AccountStatusMasterId((string)$ormEntity->account_status_master_id),
            account_status_master_code: new Vo\AccountStatusMasterCode((string)$ormEntity->account_status_master->code),
            account_status_master_name: new Vo\AccountStatusMasterName((string)$ormEntity->account_status_master->name),
            grant_account_roles: [],
            grant_account_permissions: [],
        );

        return $domainEntityAdminAccountGrant->assignGrantAccountRoles(
            array_map(
                function(OrmEntityGrantAccountRole $grantAccountRole) use ($domainEntityAdminAccountGrant) {
                    return self::toGrantAccountRole($grantAccountRole, $domainEntityAdminAccountGrant);
                }, $ormEntity->grant_account_roles
            )
        )->assignGrantAccountPermissions(
            array_map(
                function (OrmEntityGrantAccountPermission $grantAccountPermission) use ($domainEntityAdminAccountGrant) {
                    return self::toGrantAccountPermission($grantAccountPermission, $domainEntityAdminAccountGrant);
                }, $ormEntity->grant_account_permissions
            )
        );
    }

    /**
     * @param \App\Model\Entity\Shared\AccountStatusMaster $ormAccountStatusMaster
     * @return \App\Domain\Admin\AdminGrant\Entity\AccountStatusMaster
     */
    public static function toAccountStatusMaster(
        OrmAccountStatusMaster $ormAccountStatusMaster
    ): DomainAccountStatusMaster {
        return new DomainAccountStatusMaster(
            account_status_master_id: new Vo\AccountStatusMasterId((string)$ormAccountStatusMaster->id),
            account_status_master_code: new Vo\AccountStatusMasterCode((string)$ormAccountStatusMaster->code),
            account_status_master_name: new Vo\AccountStatusMasterName((string)$ormAccountStatusMaster->name),
        );
    }

    /**
     * @param \App\Model\Entity\Grant\GrantAccountRole $ormGrantAccountRole
     * @param \App\Domain\Admin\AdminGrant\Entity\AdminAccountGrant $domainEntityAdminAccountGrant
     * @return \App\Domain\Admin\AdminGrant\Entity\GrantAccountRole
     */
    private static function toGrantAccountRole(
        OrmEntityGrantAccountRole $ormGrantAccountRole, 
        DomainEntityAdminAccountGrant $domainEntityAdminAccountGrant
    ): DomainEntityGrantAccountRole {
        $domainEntityGrantAccountRole = new DomainEntityGrantAccountRole(
            grant_account_role_id: new Vo\GrantAccountRoleId((string)$ormGrantAccountRole->id),
            admin_account_id: new Vo\AdminAccountId((string)$ormGrantAccountRole->account_id),
            grant_role_id: new Vo\GrantRoleId((string)$ormGrantAccountRole->grant_role_id),
            created: new SVo\Created($ormGrantAccountRole->created->format('Y-m-d\TH:i:s')),
            modified: new SVo\Modified($ormGrantAccountRole->modified->format('Y-m-d\TH:i:s')),
            admin_account_grant : $domainEntityAdminAccountGrant,
            grant_role: null
        );

        return $domainEntityGrantAccountRole->assignGrantRole(
            self::toGrantRole($ormGrantAccountRole->grant_role, [
                $domainEntityGrantAccountRole
            ]),
        );
    }

    /**
     * @param \App\Model\Entity\Grant\GrantRole $ormGrantRole
     * @param array<\App\Domain\Admin\AdminGrant\Entity\GrantAccountRole> $grant_account_roles
     * @return \App\Domain\Admin\AdminGrant\Entity\GrantRole
     */
    private static function toGrantRole(
        OrmEntityGrantRole $ormGrantRole, 
        array $grant_account_roles = [],
    ): DomainEntityGrantRole {
        $domainEntityGrantRole = new DomainEntityGrantRole(
            grant_role_id: new Vo\GrantRoleId((string)$ormGrantRole->id),
            code: new Vo\Code((string)$ormGrantRole->code),
            name: new Vo\Name((string)$ormGrantRole->name),
            description: new Vo\Description((string)$ormGrantRole->description),
            sort: new Vo\Sort((string)$ormGrantRole->sort),
            is_active: new Vo\IsActive((string)$ormGrantRole->is_active),
            created: new SVo\Created($ormGrantRole->created->format('Y-m-d\TH:i:s')),
            modified: new SVo\Modified($ormGrantRole->modified->format('Y-m-d\TH:i:s')),
            grant_account_roles: $grant_account_roles,
            grant_role_permissions: [],
        );

        return $domainEntityGrantRole->assignGrantRolePermissions(
            array_map(
                function (OrmEntityGrantRolePermission $grantRolePermission) use ($domainEntityGrantRole) {
                    return self::toGrantRolePermission($grantRolePermission, $domainEntityGrantRole);
                }, $ormGrantRole->grant_role_permissions
            )
        );
    }

    /**
     * @param \App\Model\Entity\Grant\GrantRolePermission $ormGrantRolePermission
     * @param \App\Domain\Admin\AdminGrant\Entity\GrantRole $domainEntityGrantRole
     * @return \App\Domain\Admin\AdminGrant\Entity\GrantRolePermission
     */
    private static function toGrantRolePermission(
        OrmEntityGrantRolePermission $ormGrantRolePermission,
        DomainEntityGrantRole $domainEntityGrantRole,
    ): DomainEntityGrantRolePermission {
        return new DomainEntityGrantRolePermission(
            grant_role_permission_id: new Vo\GrantRolePermissionId((string)$ormGrantRolePermission->id),
            grant_role_id: new Vo\GrantRoleId((string)$ormGrantRolePermission->grant_role_id),
            grant_permission_id: new Vo\GrantPermissionId((string)$ormGrantRolePermission->grant_permission_id),
            created: new SVo\Created($ormGrantRolePermission->created->format('Y-m-d\TH:i:s')),
            modified: new SVo\Modified($ormGrantRolePermission->modified->format('Y-m-d\TH:i:s')),
            grant_role: $domainEntityGrantRole,
            grant_permission : self::toGrantPermission($ormGrantRolePermission->grant_permission),
        );
    }

    /**
     * @param \App\Model\Entity\Grant\GrantAccountPermission $ormGrantAccountPermission
     * @param \App\Domain\Admin\AdminGrant\Entity\AdminAccountGrant $domainEntityAdminAccountGrant
     * @return \App\Domain\Admin\AdminGrant\Entity\GrantAccountPermission
     */
    private static function toGrantAccountPermission(
        OrmEntityGrantAccountPermission $ormGrantAccountPermission,
        DomainEntityAdminAccountGrant $domainEntityAdminAccountGrant,
    ): DomainEntityGrantAccountPermission {
        return new DomainEntityGrantAccountPermission(
            grant_account_permission_id: new Vo\GrantAccountPermissionId((string)$ormGrantAccountPermission->id),
            admin_account_id: new Vo\AdminAccountId((string)$ormGrantAccountPermission->account_id),
            grant_permission_id: new Vo\GrantPermissionId((string)$ormGrantAccountPermission->grant_permission_id),
            created: new SVo\Created($ormGrantAccountPermission->created->format('Y-m-d\TH:i:s')),
            modified: new SVo\Modified($ormGrantAccountPermission->modified->format('Y-m-d\TH:i:s')),
            admin_account_grant: $domainEntityAdminAccountGrant,
            grant_permission : self::toGrantPermission($ormGrantAccountPermission->grant_permission),
        );
    }

    /**
     * @param \App\Model\Entity\Grant\GrantPermission $ormGrantPermission
     * @return \App\Domain\Admin\AdminGrant\Entity\GrantPermission
     */
    private static function toGrantPermission(
        OrmEntityGrantPermission $ormGrantPermission
    ): DomainEntityGrantPermission {
        return new DomainEntityGrantPermission(
            grant_permission_id: new Vo\GrantPermissionId((string)$ormGrantPermission->id),
            code: new Vo\Code((string)$ormGrantPermission->code),
            name: new Vo\Name((string)$ormGrantPermission->name),
            description: new Vo\Description((string)$ormGrantPermission->description),
            sort: new Vo\Sort((string)$ormGrantPermission->sort),
            is_active: new Vo\IsActive((string)$ormGrantPermission->is_active),
            created: new SVo\Created($ormGrantPermission->created->format('Y-m-d\TH:i:s')),
            modified: new SVo\Modified($ormGrantPermission->modified->format('Y-m-d\TH:i:s')),
        );
    }
}
