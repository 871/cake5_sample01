<?php
declare(strict_types=1);

namespace App\Service\Controller\Admin\AdminGrant\Role;

use App\Domain\Admin\AdminGrant\Entity\GrantRole as DomainEntity;
use App\Domain\Admin\AdminGrant\ValueObject\GrantRoleId;
use App\Infrastructure\Persistence\Cake\Admin\AdminGrant\AdminGrantRoleRepository;
use App\Security\Input\StrictCast;
use App\Service\Controller\Admin\AdminGrant as CategoryService;
use App\Service\Controller\Shared\ServiceInterface;
use App\Service\Controller\Shared\ServiceTrait;

final class Detail implements ServiceInterface
{
    use ServiceTrait;

    /**
     * @return \App\Domain\Admin\AdminGrant\Entity\GrantRole
     */
    public function getDomainEntity(): DomainEntity
    {
        return (new AdminGrantRoleRepository($this->datetime))->read(new GrantRoleId(
            StrictCast::toString($this->request->getParam('grant_role_id')),
        ));
    }

    /**
     * @return array<\App\Domain\Admin\AdminGrant\Entity\GrantPermission>
     */
    public function getGrantPermissionOptions(): array
    {
        /** @var \App\Service\Controller\Admin\AdminGrant $categoryService */
        $categoryService = $this->createService(CategoryService::class);

        return $categoryService->getAllGrantPermissionOptions();
    }
}
