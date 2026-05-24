<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Cake\Admin\AdminGrant;

use App\Domain\Admin\AdminGrant\Repository\AdminGrantPermissionRepository as DomainAdminGrantPermissionRepository;
use App\Domain\Admin\AdminGrant\SearchAdminGrantPermissionCondition;

final class AdminGrantPermissionRepository implements DomainAdminGrantPermissionRepository
{
    /**
     * コンストラクタ
     */
    public function __construct()
    {
        // do nothing
    }

    /**
     * 管理者権限の検索
     *
     * @param \App\Domain\Admin\AdminGrant\SearchAdminGrantPermissionCondition $condition
     * @return array<\App\Domain\Admin\AdminGrant\Entity\GrantPermission>
     */
    public function search(SearchAdminGrantPermissionCondition $condition): array
    {
        return (new AdminGrantPermissionRepository\Search($condition))->run();
    }
}
