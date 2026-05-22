<?php
declare(strict_types=1);

namespace App\Domain\Admin\AdminGrant\Repository;

use App\Domain\Admin\AdminGrant\SearchAdminGrantPermissionCondition;
use DateTimeInterface;

interface AdminGrantPermissionRepository
{
    /**
     * @param \DateTimeInterface $datetime
     */
    public function __construct(DateTimeInterface $datetime);

    /**
     * 管理者権限の検索
     *
     * @param \App\Domain\Admin\AdminGrant\SearchAdminGrantPermissionCondition $condition
     * @return array<\App\Domain\Admin\AdminGrant\Entity\GrantPermission>
     */
    public function search(SearchAdminGrantPermissionCondition $condition): array;
}
