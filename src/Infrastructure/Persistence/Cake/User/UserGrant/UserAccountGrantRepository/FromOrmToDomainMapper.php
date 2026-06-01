<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Cake\User\UserGrant\UserAccountGrantRepository;

use App\Domain\User\UserGrant\Entity\AccountStatusMaster as DomainAccountStatusMaster;
use App\Domain\User\UserGrant\Entity\UserAccountGrant as DomainEntityUserAccountGrant;
use App\Domain\User\UserGrant\Entity\GrantAccountPermission as DomainEntityGrantAccountPermission;
use App\Domain\User\UserGrant\Entity\GrantAccountRole as DomainEntityGrantAccountRole;
use App\Domain\User\UserGrant\Entity\GrantPermission as DomainEntityGrantPermission;
use App\Domain\User\UserGrant\Entity\GrantRole as DomainEntityGrantRole;
use App\Domain\User\UserGrant\Entity\GrantRolePermission as DomainEntityGrantRolePermission;
use App\Domain\User\UserGrant\ValueObject as Vo;
use App\Domain\Shared\ValueObject as SVo;
use App\Model\Entity\User\UserAccount as OrmEntityUserAccount;
use App\Model\Entity\Grant\GrantAccountPermission as OrmEntityGrantAccountPermission;
use App\Model\Entity\Grant\GrantAccountRole as OrmEntityGrantAccountRole;
use App\Model\Entity\Grant\GrantPermission as OrmEntityGrantPermission;
use App\Model\Entity\Grant\GrantRole as OrmEntityGrantRole;
use App\Model\Entity\Grant\GrantRolePermission as OrmEntityGrantRolePermission;
use App\Model\Entity\Shared\AccountStatusMaster as OrmAccountStatusMaster;

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
     * @param \App\Model\Entity\User\UserAccount $ormEntity
     * @return \App\Domain\User\UserGrant\Entity\UserAccountGrant
     */
    public static function toUserAccountGrant(OrmEntityUserAccount $ormEntity): DomainEntityUserAccountGrant
    {
        $domainEntityUserAccountGrant = new DomainEntityUserAccountGrant(
            user_account_id: new Vo\UserAccountId((string)$ormEntity->id),
            email: new Vo\Email((string)$ormEntity->email),
            name: new Vo\Name((string)$ormEntity->name),
            account_status_master_id: new Vo\AccountStatusMasterId((string)$ormEntity->account_status_master_id),
            account_status_master_code: new Vo\AccountStatusMasterCode((string)$ormEntity->account_status_master->code),
            account_status_master_name: new Vo\AccountStatusMasterName((string)$ormEntity->account_status_master->name),
            grant_account_roles: [],
            grant_account_permissions: [],
        );

        return $domainEntityUserAccountGrant->assignGrantAccountRoles(
            array_map(
                function (OrmEntityGrantAccountRole $grantAccountRole) use ($domainEntityUserAccountGrant) {
                    return self::toGrantAccountRole($grantAccountRole, $domainEntityUserAccountGrant);
                },
                $ormEntity->grant_account_roles,
            ),
        )->assignGrantAccountPermissions(
            array_map(
                function (
                    OrmEntityGrantAccountPermission $grantAccountPermission,
                ) use ($domainEntityUserAccountGrant) {
                    return self::toGrantAccountPermission($grantAccountPermission, $domainEntityUserAccountGrant);
                },
                $ormEntity->grant_account_permissions,
            ),
        );
    }

    /**
     * @param \App\Model\Entity\Shared\AccountStatusMaster $ormAccountStatusMaster
     * @return \App\Domain\User\UserGrant\Entity\AccountStatusMaster
     */
    public static function toAccountStatusMaster(
        OrmAccountStatusMaster $ormAccountStatusMaster,
    ): DomainAccountStatusMaster {
        return new DomainAccountStatusMaster(
            account_status_master_id: new Vo\AccountStatusMasterId((string)$ormAccountStatusMaster->id),
            account_status_master_code: new Vo\AccountStatusMasterCode((string)$ormAccountStatusMaster->code),
            account_status_master_name: new Vo\AccountStatusMasterName((string)$ormAccountStatusMaster->name),
        );
    }

    /**
     * @param \App\Model\Entity\Grant\GrantAccountRole $ormGrantAccountRole
     * @param \App\Domain\User\UserGrant\Entity\UserAccountGrant $domainEntityUserAccountGrant
     * @return \App\Domain\User\UserGrant\Entity\GrantAccountRole
     */
    private static function toGrantAccountRole(
        OrmEntityGrantAccountRole $ormGrantAccountRole,
        DomainEntityUserAccountGrant $domainEntityUserAccountGrant,
    ): DomainEntityGrantAccountRole {
        $domainEntityGrantAccountRole = new DomainEntityGrantAccountRole(
            grant_account_role_id: new Vo\GrantAccountRoleId((string)$ormGrantAccountRole->id),
            user_account_id: new Vo\UserAccountId((string)$ormGrantAccountRole->account_id),
            grant_role_id: new Vo\GrantRoleId((string)$ormGrantAccountRole->grant_role_id),
            created: new SVo\Created($ormGrantAccountRole->created->format('Y-m-d\TH:i:s')),
            modified: new SVo\Modified($ormGrantAccountRole->modified->format('Y-m-d\TH:i:s')),
            user_account_grant: $domainEntityUserAccountGrant,
            grant_role: null,
        );

        return $domainEntityGrantAccountRole->assignGrantRole(
            self::toGrantRole($ormGrantAccountRole->grant_role, [
                $domainEntityGrantAccountRole,
            ]),
        );
    }

    /**
     * @param \App\Model\Entity\Grant\GrantRole $ormGrantRole
     * @param array<\App\Domain\User\UserGrant\Entity\GrantAccountRole> $grant_account_roles
     * @return \App\Domain\User\UserGrant\Entity\GrantRole
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
                },
                $ormGrantRole->grant_role_permissions,
            ),
        );
    }

    /**
     * @param \App\Model\Entity\Grant\GrantRolePermission $ormGrantRolePermission
     * @param \App\Domain\User\UserGrant\Entity\GrantRole $domainEntityGrantRole
     * @return \App\Domain\User\UserGrant\Entity\GrantRolePermission
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
            grant_permission: self::toGrantPermission($ormGrantRolePermission->grant_permission),
        );
    }

    /**
     * @param \App\Model\Entity\Grant\GrantAccountPermission $ormGrantAccountPermission
     * @param \App\Domain\User\UserGrant\Entity\UserAccountGrant $domainEntityUserAccountGrant
     * @return \App\Domain\User\UserGrant\Entity\GrantAccountPermission
     */
    private static function toGrantAccountPermission(
        OrmEntityGrantAccountPermission $ormGrantAccountPermission,
        DomainEntityUserAccountGrant $domainEntityUserAccountGrant,
    ): DomainEntityGrantAccountPermission {
        return new DomainEntityGrantAccountPermission(
            grant_account_permission_id: new Vo\GrantAccountPermissionId((string)$ormGrantAccountPermission->id),
            user_account_id: new Vo\UserAccountId((string)$ormGrantAccountPermission->account_id),
            grant_permission_id: new Vo\GrantPermissionId((string)$ormGrantAccountPermission->grant_permission_id),
            created: new SVo\Created($ormGrantAccountPermission->created->format('Y-m-d\TH:i:s')),
            modified: new SVo\Modified($ormGrantAccountPermission->modified->format('Y-m-d\TH:i:s')),
            user_account_grant: $domainEntityUserAccountGrant,
            grant_permission: self::toGrantPermission($ormGrantAccountPermission->grant_permission),
        );
    }

    /**
     * @param \App\Model\Entity\Grant\GrantPermission $ormGrantPermission
     * @return \App\Domain\User\UserGrant\Entity\GrantPermission
     */
    private static function toGrantPermission(
        OrmEntityGrantPermission $ormGrantPermission,
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
