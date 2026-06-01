<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Cake\User\UserGrant\UserGrantRoleRepository;

use App\Domain\User\UserGrant\Entity as De;
use App\Domain\User\UserGrant\ValueObject as Vo;
use App\Domain\Shared\Enum as SEn;
use App\Domain\Shared\ValueObject as SVo;
use App\Model\Entity\User\UserAccount as OrmUserAccount;
use App\Model\Entity\Grant\GrantAccountRole as OrmGrantAccountRole;
use App\Model\Entity\Grant\GrantPermission as OrmGrantPermission;
use App\Model\Entity\Grant\GrantRole as OrmGrantRole;
use App\Model\Entity\Grant\GrantRolePermission as OrmGrantRolePermission;
use DateTimeInterface;

final class Mapper
{
    public const ACCOUNT_TYPE = SEn\AccountType::USER->value;

    /**
     * Constructor.
     */
    public function __construct(
        public readonly DateTimeInterface $datetime,
    ) {
    }

    /**
     * ORMエンティティからロールエンティティへ変換する
     *
     * @param \App\Model\Entity\Grant\GrantRole $ormGrantRole
     * @return \App\Domain\User\UserGrant\Entity\GrantRole
     */
    public function toDomainGrantRole(
        OrmGrantRole $ormGrantRole,
    ): De\GrantRole {
        $domainGrantRole = new De\GrantRole(
            grant_role_id: new Vo\GrantRoleId((string)$ormGrantRole->id),
            code: new Vo\Code((string)$ormGrantRole->code),
            name: new Vo\Name((string)$ormGrantRole->name),
            description: new Vo\Description((string)$ormGrantRole->description),
            sort: new Vo\Sort((string)$ormGrantRole->sort),
            is_active: new Vo\IsActive((string)$ormGrantRole->is_active),
            created: new SVo\Created($ormGrantRole->created->format('Y-m-d\TH:i:s')),
            modified: new SVo\Modified($ormGrantRole->modified->format('Y-m-d\TH:i:s')),
        );

        return $domainGrantRole->assignGrantAccountRoles(
            array_map(
                function (OrmGrantAccountRole $grantAccountRole) use ($domainGrantRole) {
                    return $this->toDomainGrantAccountRole($grantAccountRole, $domainGrantRole);
                },
                $ormGrantRole->grant_account_roles ?? [],
            ),
        )->assignGrantRolePermissions(
            (function () use ($ormGrantRole, $domainGrantRole): array {
                $domainGrantRolePermissions = array_map(
                    function (OrmGrantRolePermission $grantRolePermission) use ($domainGrantRole) {
                        return $this->toDomainGrantRolePermission($grantRolePermission, $domainGrantRole);
                    },
                    $ormGrantRole->grant_role_permissions ?? [],
                );

                usort($domainGrantRolePermissions, function (De\GrantRolePermission $a, De\GrantRolePermission $b) {
                    return $a->grantPermission()?->sort()->toInt() <=> $b->grantPermission()?->sort()->toInt();
                });

                return $domainGrantRolePermissions;
            })(),
        );
    }

    /**
     * ORMエンティティから付与ロールエンティティへ変換する
     *
     * @param \App\Model\Entity\Grant\GrantAccountRole $ormGrantAccountRole
     * @param \App\Domain\User\UserGrant\Entity\GrantRole $domainGrantRole
     * @return \App\Domain\User\UserGrant\Entity\GrantAccountRole
     */
    private function toDomainGrantAccountRole(
        OrmGrantAccountRole $ormGrantAccountRole,
        De\GrantRole $domainGrantRole,
    ): De\GrantAccountRole {
        $grantAccountRole = new De\GrantAccountRole(
            grant_account_role_id: new Vo\GrantAccountRoleId((string)$ormGrantAccountRole->id),
            user_account_id: new Vo\UserAccountId((string)$ormGrantAccountRole->account_id),
            grant_role_id: new Vo\GrantRoleId((string)$ormGrantAccountRole->grant_role_id),
            created: new SVo\Created($ormGrantAccountRole->created->format('Y-m-d\TH:i:s')),
            modified: new SVo\Modified($ormGrantAccountRole->modified->format('Y-m-d\TH:i:s')),
            user_account_grant: null,
            grant_role: $domainGrantRole,
        );

        // Cake ORM uses different association property names depending on the account type.
        // Try to fetch 'user_account' association, fallback to 'admin_account' if present.
        $ormUserAccount = $ormGrantAccountRole->get('user_account') ?? $ormGrantAccountRole->get('admin_account');
        /** @var OrmUserAccount|null $ormUserAccount */
        return $grantAccountRole->assignUserAccountGrant(
            $this->toDomainUserAccountGrant($ormUserAccount, $grantAccountRole),
        );
    }

    /**
     * ORMエンティティからユーザー権限エンティティへ変換する
     *
     * @param \App\Model\Entity\User\UserAccount $ormUserAccount
     * @param \App\Domain\User\UserGrant\Entity\GrantAccountRole $domainGrantAccountRole
     * @return \App\Domain\User\UserGrant\Entity\UserAccountGrant
     */
    private function toDomainUserAccountGrant(
        ?OrmUserAccount $ormUserAccount,
        De\GrantAccountRole $domainGrantAccountRole,
    ): De\UserAccountGrant {
        if ($ormUserAccount === null) {
            throw new \DomainException('User account association is missing for grant account role');
        }
        return new De\UserAccountGrant(
            user_account_id: new Vo\UserAccountId((string)$ormUserAccount->id),
            email: new Vo\Email((string)$ormUserAccount->email),
            name: new Vo\Name((string)$ormUserAccount->name),
            account_status_master_id: new Vo\AccountStatusMasterId((string)$ormUserAccount->account_status_master_id),
            account_status_master_code: new Vo\AccountStatusMasterCode((string)$ormUserAccount->account_status_master->code),
            account_status_master_name: new Vo\AccountStatusMasterName((string)$ormUserAccount->account_status_master->name),
            grant_account_roles: [$domainGrantAccountRole],
            grant_account_permissions: [],
        );
    }

    /**
     * ORMエンティティからロール権限エンティティへ変換する
     *
     * @param \App\Model\Entity\Grant\GrantRolePermission $ormGrantRolePermission
     * @param \App\Domain\User\UserGrant\Entity\GrantRole $domainGrantRole
     * @return \App\Domain\User\UserGrant\Entity\GrantRolePermission
     */
    private function toDomainGrantRolePermission(
        OrmGrantRolePermission $ormGrantRolePermission,
        De\GrantRole $domainGrantRole,
    ): De\GrantRolePermission {
        return new De\GrantRolePermission(
            grant_role_permission_id: new Vo\GrantRolePermissionId((string)$ormGrantRolePermission->id),
            grant_role_id: new Vo\GrantRoleId((string)$ormGrantRolePermission->grant_role_id),
            grant_permission_id: new Vo\GrantPermissionId((string)$ormGrantRolePermission->grant_permission_id),
            created: new SVo\Created($ormGrantRolePermission->created->format('Y-m-d\TH:i:s')),
            modified: new SVo\Modified($ormGrantRolePermission->modified->format('Y-m-d\TH:i:s')),
            grant_role: $domainGrantRole,
            grant_permission: $ormGrantRolePermission->grant_permission
                ? $this->toDomainGrantPermission($ormGrantRolePermission->grant_permission)
                : null,
        );
    }

    /**
     * ORMエンティティから権限エンティティへ変換する
     *
     * @param \App\Model\Entity\Grant\GrantPermission $ormGrantPermission
     * @return \App\Domain\User\UserGrant\Entity\GrantPermission
     */
    public function toDomainGrantPermission(
        OrmGrantPermission $ormGrantPermission,
    ): De\GrantPermission {
        return new De\GrantPermission(
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
