<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Cake\User\UserGrant;

use App\Domain\User\UserGrant\Entity\GrantAccountRole;
use App\Domain\User\UserGrant\Entity\GrantAccountPermission;
use App\Domain\User\UserGrant\Repository\UserAccountGrantRepository as InterfaceRepo;
use App\Domain\User\UserGrant\ValueObject as Vo;
use App\Infrastructure\Persistence\Cake\User\UserGrant\UserGrantRoleRepository\Mapper as RoleMapper;
use App\Infrastructure\Persistence\Cake\User\UserGrant\UserGrantPermissionRepository\Mapper as PermissionMapper;
use App\Lib\UUID\UUID;
use App\Model\Table\Grant\GrantAccountRolesTable;
use App\Model\Table\Grant\GrantAccountPermissionsTable;
use Cake\ORM\Locator\LocatorAwareTrait;
use DateTimeInterface;

final class UserAccountGrantRepository implements InterfaceRepo
{
    use LocatorAwareTrait;

    public function __construct(private readonly DateTimeInterface $datetime)
    {
    }

    public function findRolesByUserAccountId(Vo\UserAccountId $userAccountId): array
    {
        $table = $this->fetchTable(GrantAccountRolesTable::class);

        /** @var \App\Model\Entity\Grant\GrantAccountRole[] $rows */
        $rows = $table->find()
            ->contain(['GrantRoles' => ['GrantRolePermissions' => ['GrantPermissions']]])
            ->where([
                'GrantAccountRoles.account_type' => RoleMapper::ACCOUNT_TYPE,
                'GrantAccountRoles.account_id' => $userAccountId->toString(),
            ])
            ->all()
            ->toList();

        $mapper = new RoleMapper($this->datetime);
        $result = [];
        foreach ($rows as $orm) {
            /** @var \App\Model\Entity\Grant\GrantAccountRole $orm */
            if (empty($orm->grant_role)) {
                continue;
            }
            $domainRole = $mapper->toDomainGrantRole($orm->grant_role);
            foreach ($domainRole->grantAccountRoles() as $gar) {
                if ($gar->hasUserAccountId($userAccountId)) {
                    $result[] = $gar;
                }
            }
        }

        return $result;
    }

    public function findPermissionsByUserAccountId(Vo\UserAccountId $userAccountId): array
    {
        $table = $this->fetchTable(GrantAccountPermissionsTable::class);

        /** @var \App\Model\Entity\Grant\GrantAccountPermission[] $rows */
        $rows = $table->find()
            ->contain(['GrantPermissions'])
            ->where([
                'GrantAccountPermissions.account_type' => PermissionMapper::ACCOUNT_TYPE,
                'GrantAccountPermissions.account_id' => $userAccountId->toString(),
            ])
            ->all()
            ->toList();

        $mapper = new PermissionMapper();
        $result = [];
        foreach ($rows as $orm) {
            /** @var \App\Model\Entity\Grant\GrantAccountPermission $orm */
            if (empty($orm->grant_permission)) {
                continue;
            }
            $domainPermission = $mapper->toDomainGrantPermission($orm->grant_permission);
            $entity = new GrantAccountPermission(
                new Vo\GrantAccountPermissionId((string)$orm->id),
                new Vo\UserAccountId((string)$orm->account_id),
                new Vo\GrantPermissionId((string)$orm->grant_permission_id),
                new \App\Domain\Shared\ValueObject\Created($orm->created->format('Y-m-d\\TH:i:s')),
                new \App\Domain\Shared\ValueObject\Modified($orm->modified->format('Y-m-d\\TH:i:s')),
                null,
                $domainPermission,
            );
            $result[] = $entity;
        }

        return $result;
    }

    public function save(array $roles, array $permissions): void
    {
        $rolesTable = $this->fetchTable(GrantAccountRolesTable::class);
        $permsTable = $this->fetchTable(GrantAccountPermissionsTable::class);

        $this->fetchTable(GrantAccountRolesTable::class)->getConnection()->transactional(function () use ($rolesTable, $permsTable, $roles, $permissions) {
            $accountId = null;
            if ($roles !== []) {
                $accountId = $roles[0]->userAccountId()->toString();
            } elseif ($permissions !== []) {
                $accountId = $permissions[0]->userAccountId()->toString();
            }

            if ($accountId === null) {
                return;
            }

            $rolesTable->deleteAll([
                'account_type' => RoleMapper::ACCOUNT_TYPE,
                'account_id' => $accountId,
            ]);

            $permsTable->deleteAll([
                'account_type' => PermissionMapper::ACCOUNT_TYPE,
                'account_id' => $accountId,
            ]);

            if ($roles !== []) {
                $data = array_map(function (GrantAccountRole $role) {
                    return [
                        'id' => UUID::uuid7(),
                        'account_type' => RoleMapper::ACCOUNT_TYPE,
                        'account_id' => $role->userAccountId()->toString(),
                        'grant_role_id' => $role->grantRoleId()->toString(),
                        'created' => $this->datetime->format('Y-m-d\TH:i:s'),
                        'modified' => $this->datetime->format('Y-m-d\TH:i:s'),
                    ];
                }, $roles);

                $entities = $rolesTable->newEntities($data);
                $rolesTable->saveManyOrFail($entities, ['checkExisting' => false]);
            }

            if ($permissions !== []) {
                $data = array_map(function (GrantAccountPermission $perm) {
                    return [
                        'id' => UUID::uuid7(),
                        'account_type' => PermissionMapper::ACCOUNT_TYPE,
                        'account_id' => $perm->userAccountId()->toString(),
                        'grant_permission_id' => $perm->grantPermissionId()->toString(),
                        'created' => $this->datetime->format('Y-m-d\TH:i:s'),
                        'modified' => $this->datetime->format('Y-m-d\TH:i:s'),
                    ];
                }, $permissions);

                $entities = $permsTable->newEntities($data);
                $permsTable->saveManyOrFail($entities, ['checkExisting' => false]);
            }
        });
    }
}
