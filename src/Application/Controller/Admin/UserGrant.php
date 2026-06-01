<?php
declare(strict_types=1);

namespace App\Application\Controller\Admin;

use App\Application\Controller\Shared\ServiceInterface;
use App\Application\Controller\Shared\ServiceTrait;
use App\Domain\User\UserGrant\SearchUserGrantPermissionCondition;
use App\Domain\User\UserGrant\SearchUserGrantRoleCondition;
use App\Domain\User\UserGrant\ValueObject as Vo;
use App\Domain\Shared\ValueObject as SVo;
use App\Infrastructure\Persistence\Cake\User\UserGrant\UserAccountGrantRepository;
use App\Infrastructure\Persistence\Cake\User\UserGrant\UserGrantPermissionRepository;
use App\Infrastructure\Persistence\Cake\User\UserGrant\UserGrantRoleRepository;

final class UserGrant implements ServiceInterface
{
    use ServiceTrait;

    /**
     * @return array<\App\Domain\User\UserGrant\Entity\GrantRole>
     */
    public function getGrantRoleOptions(): array
    {
        return (new UserGrantRoleRepository($this->datetime))->search(
            condition: new SearchUserGrantRoleCondition(
                searchText: new SVo\SearchText(null),
                isActives: [
                    new Vo\IsActive('1'),
                ],
                grantPermissionIds: [],
            ),
        );
    }

    /**
     * @return array<\App\Domain\User\UserGrant\Entity\GrantPermission>
     */
    public function getGrantPermissionOptions(): array
    {
        return (new UserGrantPermissionRepository($this->datetime))->search(
            condition: new SearchUserGrantPermissionCondition(
                searchText: new SVo\SearchText(null),
                isActive: new Vo\IsActive('1'),
            ),
        );
    }

    /**
     * @return array<\App\Domain\User\UserGrant\Entity\GrantPermission>
     */
    public function getAllGrantPermissionOptions(): array
    {
        $grantPermissions = [
            ...(new UserGrantPermissionRepository($this->datetime))->search(
                condition: new SearchUserGrantPermissionCondition(
                    searchText: new SVo\SearchText(null),
                    isActive: new Vo\IsActive('1'),
                ),
            ),
            ...(new UserGrantPermissionRepository($this->datetime))->search(
                condition: new SearchUserGrantPermissionCondition(
                    searchText: new SVo\SearchText(null),
                    isActive: new Vo\IsActive('0'),
                ),
            ),
        ];

        usort($grantPermissions, static function ($a, $b): int {
            return $a->sort()->toInt() <=> $b->sort()->toInt()
                ?: $a->grantPermissionId()->toInt() <=> $b->grantPermissionId()->toInt();
        });

        return $grantPermissions;
    }

    /**
     * @return array<\App\Domain\User\UserGrant\Entity\AccountStatusMaster>
     */
    public function getAccountStatusOptions(): array
    {
        return (new UserAccountGrantRepository($this->datetime))->findAccountStatusMasters();
    }
}
