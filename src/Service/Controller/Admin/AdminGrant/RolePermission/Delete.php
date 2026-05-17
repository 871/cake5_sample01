<?php
declare(strict_types=1);

namespace App\Service\Controller\Admin\AdminGrant\RolePermission;

use App\Domain\Admin\AdminGrant\ValueObject\GrantRolePermissionId;
use App\Infrastructure\Persistence\Cake\Admin\AdminGrantRepository;
use App\Service\Controller\Shared\ServiceInterface;
use App\Service\Controller\Shared\ServiceTrait;

final class Delete implements ServiceInterface
{
    use ServiceTrait;

    /**
     * @param string $id
     * @return void
     */
    public function delete(string $id): void
    {
        (new AdminGrantRepository($this->datetime))->deleteRolePermission(new GrantRolePermissionId($id));
    }
}
