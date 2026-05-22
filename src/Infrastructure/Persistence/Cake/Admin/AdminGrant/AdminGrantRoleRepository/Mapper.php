<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Cake\Admin\AdminGrant\AdminGrantRoleRepository;

use App\Domain\Admin\AdminGrant\Entity as De;
use App\Domain\Admin\AdminGrant\ValueObject as Vo;
use App\Domain\Shared\Enum as SEn;
use App\Domain\Shared\ValueObject as SVo;
use App\Model\Entity\Admin\AdminAccount as OrmAdminAccount;
use App\Model\Entity\Grant\GrantAccountRole as OrmGrantAccountRole;
use App\Model\Entity\Grant\GrantPermission as OrmGrantPermission;
use App\Model\Entity\Grant\GrantRole as OrmGrantRole;
use App\Model\Entity\Grant\GrantRolePermission as OrmGrantRolePermission;
use App\Model\Table\Grant\GrantRolesTable;
use Cake\ORM\Locator\LocatorAwareTrait;
use DateTimeInterface;

final class Mapper
{
    use LocatorAwareTrait;

    public const ACCOUNT_TYPE = SEn\AccountType::ADMIN->value;

    /**
     * @var \App\Model\Table\Grant\GrantRolesTable
     */
    private GrantRolesTable $table;

    /**
     * Constructor.
     */
    public function __construct(
        public readonly DateTimeInterface $datetime,
    ) {
        $this->table = $this->fetchTable(GrantRolesTable::class);
    }

    /**
     * ORMエンティティからロールエンティティへ変換する
     *
     * @param \App\Model\Entity\Grant\GrantRole $ormGrantRole
     * @return \App\Domain\Admin\AdminGrant\Entity\GrantRole
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
            grant_account_roles: [],
            grant_role_permissions: [],
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

                uksort($domainGrantRolePermissions, function (De\GrantRolePermission $a, De\GrantRolePermission $b) {
                    return $a->grantPermission()->sort()->toInt() <=> $b->grantPermission()->sort()->toInt();
                });

                return $domainGrantRolePermissions;
            })(),
        );
    }

    /**
     * ORMエンティティから付与ロールエンティティへ変換する
     *
     * @param \App\Model\Entity\Grant\GrantAccountRole $ormGrantAccountRole
     * @param \App\Domain\Admin\AdminGrant\Entity\GrantRole $domainGrantRole
     * @return \App\Domain\Admin\AdminGrant\Entity\GrantAccountRole
     */
    private function toDomainGrantAccountRole(
        OrmGrantAccountRole $ormGrantAccountRole,
        De\GrantRole $domainGrantRole,
    ): De\GrantAccountRole {
        $grantAccountRole = new De\GrantAccountRole(
            grant_account_role_id: new Vo\GrantAccountRoleId((string)$ormGrantAccountRole->id),
            admin_account_id: new Vo\AdminAccountId((string)$ormGrantAccountRole->account_id),
            grant_role_id: new Vo\GrantRoleId((string)$ormGrantAccountRole->grant_role_id),
            created: new SVo\Created($ormGrantAccountRole->created->format('Y-m-d\TH:i:s')),
            modified: new SVo\Modified($ormGrantAccountRole->modified->format('Y-m-d\TH:i:s')),
            admin_account_grant: null,
            grant_role: $domainGrantRole,
        );

        return $grantAccountRole->assignAdminAccountGrant(
            $this->toDomainAdminAccountGrant($ormGrantAccountRole->admin_account, $grantAccountRole),
        );
    }

    /**
     * ORMエンティティから管理者権限エンティティへ変換する
     *
     * @param \App\Model\Entity\Admin\AdminAccount $ormAdminAccount
     * @param \App\Domain\Admin\AdminGrant\Entity\GrantAccountRole $domainGrantAccountRole
     * @return \App\Domain\Admin\AdminGrant\Entity\AdminAccountGrant
     */
    private function toDomainAdminAccountGrant(
        OrmAdminAccount $ormAdminAccount,
        De\GrantAccountRole $domainGrantAccountRole,
    ): De\AdminAccountGrant {
        return new De\AdminAccountGrant(
            admin_account_id: new Vo\AdminAccountId((string)$ormAdminAccount->id),
            email: new Vo\Email((string)$ormAdminAccount->email),
            name: new Vo\Name((string)$ormAdminAccount->name),
            admin_note: new Vo\AdminNote((string)$ormAdminAccount->admin_note),
            account_status_master_id: new Vo\AccountStatusMasterId(
                (string)$ormAdminAccount->account_status_master_id,
            ),
            account_status_master_code: new Vo\AccountStatusMasterCode(
                (string)$ormAdminAccount->account_status_master_code,
            ),
            account_status_master_name: new Vo\AccountStatusMasterName(
                (string)$ormAdminAccount->account_status_master_name,
            ),
            grant_account_roles: [
                $domainGrantAccountRole,
            ],
            grant_account_permissions: [],
        );
    }

    /**
     * ORMエンティティからロール権限エンティティへ変換する
     *
     * @param \App\Model\Entity\Grant\GrantRolePermission $ormGrantRolePermission
     * @param \App\Domain\Admin\AdminGrant\Entity\GrantRole $domainGrantRole
     * @return \App\Domain\Admin\AdminGrant\Entity\GrantRolePermission
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
            grant_permission: $this->toDomainGrantPermission($ormGrantRolePermission->grant_permission),
        );
    }

    /**
     * ORMエンティティから権限エンティティへ変換する
     *
     * @param \App\Model\Entity\Grant\GrantPermission $ormGrantPermission
     * @return \App\Domain\Admin\AdminGrant\Entity\GrantPermission
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

    /**
     * 新規保存用のORMエンティティへ変換する
     *
     * @param \App\Domain\Admin\AdminGrant\Entity\GrantRole $domainGrantRole
     * @return \App\Model\Entity\Grant\GrantRole
     */
    public function toNewOrmGrantRole(
        De\GrantRole $domainGrantRole,
    ): OrmGrantRole {
        /** @var \App\Model\Entity\Grant\GrantRole */
        return $this->table->newEntity([
            'account_type' => self::ACCOUNT_TYPE,
            'code' => $domainGrantRole->code()->toString(),
            'name' => $domainGrantRole->name()->toString(),
            'description' => $domainGrantRole->description()->toString(),
            'sort' => $domainGrantRole->sort()->toInt(),
            'is_active' => $domainGrantRole->isActive()->toInt(),
            'created' => $domainGrantRole->created()->format('Y-m-d\TH:i:s'),
            'modified' => $domainGrantRole->modified()->format('Y-m-d\TH:i:s'),
        ]);
    }
}
