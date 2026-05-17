<?php
declare(strict_types=1);

namespace App\Service\Controller\Admin\AdminGrant\RolePermission;

use App\Domain\Admin\AdminGrant\Entity\GrantRolePermission;
use App\Infrastructure\Persistence\Cake\Admin\AdminGrantRepository;
use App\Service\Controller\Admin\AdminGrant as CategoryService;
use App\Service\Controller\Shared\ServiceInterface;
use App\Service\Controller\Shared\ServiceTrait;

final class Create implements ServiceInterface
{
    use ServiceTrait;

    /**
     * @return array<int, array<string, string>>
     */
    public function getGrantRoleOptions(): array
    {
        /** @var \App\Service\Controller\Admin\AdminGrant $category */
        $category = $this->createService(CategoryService::class);

        return $category->getGrantRoleOptions();
    }

    /**
     * @return array<int, array<string, string>>
     */
    public function getGrantPermissionOptions(): array
    {
        /** @var \App\Service\Controller\Admin\AdminGrant $category */
        $category = $this->createService(CategoryService::class);

        return $category->getGrantPermissionOptions();
    }

    /**
     * @return void
     */
    public function createFromRequest(): void
    {
        $data = (array)$this->request->getData();

        (new AdminGrantRepository($this->datetime))->createRolePermission(new GrantRolePermission(
            id: null,
            grant_role_id: (string)($data['grant_role_id'] ?? ''),
            grant_permission_id: (string)($data['grant_permission_id'] ?? ''),
            created: $this->datetime->format('Y-m-d\TH:i:s'),
            modified: $this->datetime->format('Y-m-d\TH:i:s'),
        ));
    }
}
