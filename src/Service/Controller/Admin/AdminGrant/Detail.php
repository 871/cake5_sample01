<?php
declare(strict_types=1);

namespace App\Service\Controller\Admin\AdminGrant;


use App\Domain\Admin\AdminGrant\SearchAdminGrantPermissionCondition;
use App\Domain\Admin\AdminGrant\ValueObject as Vo;
use App\Domain\Shared\ValueObject as SVo;
use App\Domain\Admin\AdminGrant\Entity\AdminAccountGrant;
use App\Infrastructure\Persistence\Cake\Admin\AdminGrant\AdminAccountGrantRepository;
use App\Infrastructure\Persistence\Cake\Admin\AdminGrant\AdminGrantPermissionRepository;
use App\Service\Controller\Shared\ServiceInterface;
use App\Service\Controller\Shared\ServiceTrait;

final class Detail implements ServiceInterface
{
    use ServiceTrait;

    /**
     * @param string $adminAccountId
     * @return AdminAccountGrant
     */
    public function getAdminAccountGrant(string $adminAccountId): AdminAccountGrant
    {
        return (new AdminAccountGrantRepository($this->datetime))->detail(new Vo\AdminAccountId($adminAccountId));
    }

    /**
     * @return array<\App\Domain\Admin\AdminGrant\Entity\GrantPermission>
     */
    public function getGrantPermissions(): array
    {
        return (new AdminGrantPermissionRepository($this->datetime))->search(new SearchAdminGrantPermissionCondition(
            searchText: new SVo\SearchText(''),
            isActive: 1,
        ));
    }
}
