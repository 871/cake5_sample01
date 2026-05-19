<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Cake\Admin\AdminGrant;

use App\Domain\Admin\AdminGrant\Repository\AdminGrantPermissionRepository as DomainAdminGrantPermissionRepository;
use App\Domain\Admin\AdminGrant\SearchAdminGrantPermissionCondition;
use DateTimeInterface;

final class AdminGrantPermissionRepository implements DomainAdminGrantPermissionRepository
{
    /**
     * @param \DateTimeInterface $datetime
     */
    public function __construct(
        private readonly DateTimeInterface $datetime,
    ) {
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
