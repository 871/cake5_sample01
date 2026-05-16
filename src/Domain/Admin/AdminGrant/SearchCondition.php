<?php
declare(strict_types=1);

namespace App\Domain\Admin\AdminGrant;

use App\Domain\Admin\AdminGrant\ValueObject as Vo;

class SearchCondition
{
    /**
     * @param \App\Domain\Admin\AdminGrant\ValueObject\AdminAccountId[] $adminAccountIds
     * @param \App\Domain\Admin\AdminGrant\ValueObject\AccountStatusMasterId[] $accountStatusMasterIds
     * @param \App\Domain\Admin\AdminGrant\ValueObject\GrantRoleId[] $grantRoleIds
     * @param \App\Domain\Admin\AdminGrant\ValueObject\GrantPermissionId[] $grantPermissionIds
     */
    public function __construct(
        /** @var \App\Domain\Admin\AdminGrant\ValueObject\AdminAccountId[] */
        private readonly array $adminAccountIds,
        /** @var \App\Domain\Admin\AdminGrant\ValueObject\AccountStatusMasterId[] */
        private readonly array $accountStatusMasterIds,
        /** @var \App\Domain\Admin\AdminGrant\ValueObject\GrantRoleId[] */
        private readonly array $grantRoleIds,
        /** @var \App\Domain\Admin\AdminGrant\ValueObject\GrantPermissionId[] */
        private readonly array $grantPermissionIds,
    ) {
        // 処理なし
    }

    /**
     * @return \App\Domain\Admin\AdminGrant\ValueObject\AdminAccountId[]
     */
    public function getAdminAccountIds(): array
    {
        return $this->adminAccountIds;
    }

    /**
     * @return \App\Domain\Admin\AdminGrant\ValueObject\AccountStatusMasterId[]
     */
    public function getAccountStatusMasterIds(): array
    {
        return $this->accountStatusMasterIds;
    }

    /**
     * @return \App\Domain\Admin\AdminGrant\ValueObject\GrantRoleId[]
     */
    public function getGrantRoleIds(): array
    {
        return $this->grantRoleIds;
    }

    /**
     * @return \App\Domain\Admin\AdminGrant\ValueObject\GrantPermissionId[]
     */
    public function getGrantPermissionIds(): array
    {
        return $this->grantPermissionIds;
    }
}
