<?php
declare(strict_types=1);

namespace App\Application\Controller\Admin\AdminGrant;

use App\Application\Controller\Shared\ServiceInterface;
use App\Application\Controller\Shared\ServiceTrait;
use App\Domain\Admin\AdminGrant\Entity\AdminAccountGrant;
use App\Domain\Admin\AdminGrant\SearchAdminGrantPermissionCondition;
use App\Domain\Admin\AdminGrant\ValueObject as Vo;
use App\Domain\Shared\ValueObject as SVo;
use App\Infrastructure\Persistence\Cake\Admin\AdminGrant\AdminAccountGrantRepository;
use App\Infrastructure\Persistence\Cake\Admin\AdminGrant\AdminGrantPermissionRepository;

final class Detail implements ServiceInterface
{
    use ServiceTrait;

    /**
     * @param string $adminAccountId
     * @return \App\Domain\Admin\AdminGrant\Entity\AdminAccountGrant
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
        return (new AdminGrantPermissionRepository())->search(new SearchAdminGrantPermissionCondition(
            searchText: new SVo\SearchText(''),
            isActive: new Vo\IsActive('1'),
        ));
    }
}
