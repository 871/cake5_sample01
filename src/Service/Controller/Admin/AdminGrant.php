<?php
declare(strict_types=1);

namespace App\Service\Controller\Admin;

use App\Domain\Admin\AdminGrant\SearchAdminGrantPermissionCondition;
use App\Domain\Admin\AdminGrant\SearchAdminGrantRoleCondition;
use App\Domain\Admin\AdminGrant\ValueObject as Vo;
use App\Domain\Shared\ValueObject as SVo;
use App\Infrastructure\Persistence\Cake\Admin\AdminGrant\AdminAccountGrantRepository;
use App\Infrastructure\Persistence\Cake\Admin\AdminGrant\AdminGrantPermissionRepository;
use App\Infrastructure\Persistence\Cake\Admin\AdminGrant\AdminGrantRoleRepository;
use App\Service\Controller\Shared\ServiceInterface;
use App\Service\Controller\Shared\ServiceTrait;

final class AdminGrant implements ServiceInterface
{
    use ServiceTrait;

    /**
     * @return array<\App\Domain\Admin\AdminGrant\Entity\GrantRole>
     */
    public function getGrantRoleOptions(): array
    {
        return (new AdminGrantRoleRepository($this->datetime))->search(
            condition: new SearchAdminGrantRoleCondition(
                searchText: new SVo\SearchText(null),
                isActives: [
                    new Vo\IsActive('1'),
                ],
                grantPermissionIds: [],
            ),
        );
    }

    /**
     * @return array<\App\Domain\Admin\AdminGrant\Entity\GrantPermission>
     */
    public function getGrantPermissionOptions(): array
    {
        return (new AdminGrantPermissionRepository($this->datetime))->search(
            condition: new SearchAdminGrantPermissionCondition(
                searchText: new SVo\SearchText(null),
                isActive: new Vo\IsActive('1'),
            ),
        );
    }

    /**
     * @return array<\App\Domain\Admin\AdminGrant\Entity\GrantPermission>
     */
    public function getAllGrantPermissionOptions(): array
    {
        $grantPermissions = [
            ...(new AdminGrantPermissionRepository($this->datetime))->search(
                condition: new SearchAdminGrantPermissionCondition(
                    searchText: new SVo\SearchText(null),
                    isActive: new Vo\IsActive('1'),
                ),
            ),
            ...(new AdminGrantPermissionRepository($this->datetime))->search(
                condition: new SearchAdminGrantPermissionCondition(
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
     * @return array<\App\Domain\Admin\AdminGrant\Entity\AccountStatusMaster>
     */
    public function getAccountStatusOptions(): array
    {
        return (new AdminAccountGrantRepository($this->datetime))->findAccountStatusMasters();
    }
}
