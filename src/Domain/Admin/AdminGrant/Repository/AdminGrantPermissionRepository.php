<?php
declare(strict_types=1);

namespace App\Domain\Admin\AdminGrant\Repository;

use App\Domain\Admin\AdminGrant\SearchAdminGrantPermissionCondition;

interface AdminGrantPermissionRepository
{
    /**
     * コンストラクタ
     */
    public function __construct();

    /**
     * 管理者権限の検索
     *
     * @param \App\Domain\Admin\AdminGrant\SearchAdminGrantPermissionCondition $condition
     * @return array<\App\Domain\Admin\AdminGrant\Entity\GrantPermission>
     */
    public function search(SearchAdminGrantPermissionCondition $condition): array;
}
