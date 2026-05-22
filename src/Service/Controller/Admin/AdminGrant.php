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
     * @return array<int, array<string, string>>
     */
    public function getGrantRoleOptions(): array
    {
        return (new AdminGrantRoleRepository($this->datetime))->search(
            condition: new SearchAdminGrantRoleCondition(
                searchText: new SVo\SearchText(null),
                isActive: new Vo\IsActive('1'),
            ),
        );
    }

    /**
     * @return array<int, array<string, string>>
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
     * @return array<int, array<string, string>>
     */
    public function getAccountStatusOptions(): array
    {
        return (new AdminAccountGrantRepository($this->datetime))->findAccountStatusMasters();
    }
}
