<?php
declare(strict_types=1);

namespace App\Service\Controller\Admin\AdminGrant\RolePermission;

use App\Domain\Admin\AdminGrant\Entity\GrantRolePermission;
use App\Domain\Admin\AdminGrant\ValueObject\GrantRolePermissionId;
use App\Infrastructure\Persistence\Cake\Admin\AdminGrantRepository;
use App\Service\Controller\Shared\ServiceInterface;
use App\Service\Controller\Shared\ServiceTrait;

final class Detail implements ServiceInterface
{
    use ServiceTrait;

    /**
     * @param string $id
     * @return \App\Domain\Admin\AdminGrant\Entity\GrantRolePermission
     */
    public function read(string $id): GrantRolePermission
    {
        return (new AdminGrantRepository($this->datetime))->readRolePermission(new GrantRolePermissionId($id));
    }
}
