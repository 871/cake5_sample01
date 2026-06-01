<?php
declare(strict_types=1);

namespace App\Application\Controller\Admin\UserGrant\Role;

use App\Application\Controller\Admin\UserGrant as CategoryService;
use App\Application\Controller\Shared\ServiceInterface;
use App\Application\Controller\Shared\ServiceTrait;
use App\Domain\User\UserGrant\Entity\GrantRole as DomainEntity;
use App\Domain\User\UserGrant\ValueObject\GrantRoleId;
use App\Infrastructure\Persistence\Cake\User\UserGrant\UserGrantRoleRepository;
use App\Security\Input\StrictCast;

final class Detail implements ServiceInterface
{
    use ServiceTrait;

    /**
     * @return \App\Domain\User\UserGrant\Entity\GrantRole
     */
    public function getDomainEntity(): DomainEntity
    {
        return (new UserGrantRoleRepository($this->datetime))->read(new GrantRoleId(
            StrictCast::toString($this->request->getParam('grant_role_id')),
        ));
    }

    /**
     * @return array<\App\Domain\User\UserGrant\Entity\GrantPermission>
     */
    public function getGrantPermissionOptions(): array
    {
        /** @var \App\Application\Controller\Admin\UserGrant $categoryService */
        $categoryService = $this->createService(CategoryService::class);

        return $categoryService->getAllGrantPermissionOptions();
    }
}
