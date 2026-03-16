<?php
declare(strict_types=1);

namespace App\Domain\Admin\AdminRoles\Repository;

use App\Domain\Admin\AdminRoles\Entity\AdminRole;

interface AdminRolesRepository
{
    /**
     * 全件取得
     *
     * @return \App\Domain\Admin\AdminRoles\Entity\AdminRole[]
     */
    public function findAll(): array;
}
