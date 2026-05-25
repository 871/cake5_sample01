<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Cake\Admin\AdminGrant;

use App\Domain\Admin\AdminGrant\Repository\AdminGrantPermissionRepository as DomainAdminGrantPermissionRepository;
use App\Domain\Admin\AdminGrant\SearchAdminGrantPermissionCondition;
use App\Domain\Admin\AdminGrant\ValueObject as Vo;

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

    /**
     * 管理者が特定の権限を持っているか
     *
     * @param \App\Domain\Admin\AdminGrant\ValueObject\Code $code
     * @param \App\Domain\Admin\AdminGrant\ValueObject\AdminAccountId $accountId
     * @return bool
     */
    public function hasPermission(Vo\Code $code, Vo\AdminAccountId $accountId): bool
    {
        return (new AdminGrantPermissionRepository\HasPermission())->run($code, $accountId);
    }
}
