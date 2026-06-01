<?php
declare(strict_types=1);

namespace App\Domain\User\UserGrant\Repository;

use App\Domain\User\UserGrant\Entity\GrantAccountRole;
use App\Domain\User\UserGrant\Entity\GrantAccountPermission;
use App\Domain\User\UserGrant\ValueObject as Vo;
use DateTimeInterface;

interface UserAccountGrantRepository
{
    /**
     * @param \DateTimeInterface $datetime
     */
    public function __construct(DateTimeInterface $datetime);

    /**
     * @param Vo\UserAccountId $userAccountId
     * @return array<GrantAccountRole>
     */
    public function findRolesByUserAccountId(Vo\UserAccountId $userAccountId): array;

    /**
     * @param Vo\UserAccountId $userAccountId
     * @return array<GrantAccountPermission>
     */
    public function findPermissionsByUserAccountId(Vo\UserAccountId $userAccountId): array;

    /**
     * @param array<GrantAccountRole> $roles
     * @param array<GrantAccountPermission> $permissions
     * @return void
     */
    public function save(array $roles, array $permissions): void;
}
