<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Cake\Admin;

use App\Domain\Admin\AdminRoles\Entity\AdminRole as DomainEntity;
use App\Domain\Admin\AdminRoles\Repository\AdminRolesRepository as DomainAdminRolesRepository;

final class AdminRolesRepository implements DomainAdminRolesRepository
{
    /**
     * 全件取得
     *
     * @return \App\Domain\Admin\AdminRoles\Entity\AdminRole[]
     */
    public function findAll(): array
    {
        return (new AdminRolesRepository\FindAll())->run();
    }
}
