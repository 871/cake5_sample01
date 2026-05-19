<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Cake\Admin\AdminGrantRepository;

use App\Domain\Admin\AdminGrant\ValueObject\AdminAccountId;
use App\Domain\Admin\AdminGrant\ValueObject\GrantPermissionId;
use App\Infrastructure\Persistence\Cake\Admin\AdminGrantMapper;
use App\Model\Table\Grant\GrantAccountPermissionsTable;
use Cake\ORM\Locator\LocatorAwareTrait;

final class HasPermission
{
    use LocatorAwareTrait;

    /**
     * @var \App\Model\Table\Grant\GrantAccountPermissionsTable
     */
    private GrantAccountPermissionsTable $accountPermissionsTable;

    /**
     * @param \App\Domain\Admin\AdminGrant\ValueObject\AdminAccountId $adminAccountId
     * @param \App\Domain\Admin\AdminGrant\ValueObject\GrantPermissionId $grantPermissionId
     */
    public function __construct(
        private readonly AdminAccountId $adminAccountId,
        private readonly GrantPermissionId $grantPermissionId,
    ) {
        $this->accountPermissionsTable = $this->fetchTable(GrantAccountPermissionsTable::class);
    }

    /**
     * @return bool
     */
    public function run(): bool
    {
        $accountId = $this->adminAccountId->toInt();
        $permissionId = $this->grantPermissionId->toInt();

        $sql = <<<SQL
SELECT EXISTS(
    SELECT 1 FROM `grant_account_permissions`
    WHERE `account_type` = :ACCOUNT_TYPE1
      AND `account_id` = :ACCOUNT_ID1
      AND `grant_permission_id` = :PERMISSION_ID1
    UNION ALL
    SELECT 1 FROM `grant_account_roles` AS `gar`
    INNER JOIN `grant_role_permissions` AS `grp`
        ON `gar`.`grant_role_id` = `grp`.`grant_role_id`
    WHERE `gar`.`account_type` = :ACCOUNT_TYPE2
      AND `gar`.`account_id` = :ACCOUNT_ID2
      AND `grp`.`account_type` = :ACCOUNT_TYPE3
      AND `grp`.`grant_permission_id` = :PERMISSION_ID2
    LIMIT 1
) AS `has_permission`
SQL;

        $stmt = $this->accountPermissionsTable->getConnection()->execute($sql, [
            'ACCOUNT_TYPE1' => AdminGrantMapper::ACCOUNT_TYPE,
            'ACCOUNT_ID1' => $accountId,
            'PERMISSION_ID1' => $permissionId,
            'ACCOUNT_TYPE2' => AdminGrantMapper::ACCOUNT_TYPE,
            'ACCOUNT_ID2' => $accountId,
            'ACCOUNT_TYPE3' => AdminGrantMapper::ACCOUNT_TYPE,
            'PERMISSION_ID2' => $permissionId,
        ]);

        /** @var array<string, mixed>|false $row */
        $row = $stmt->fetch('assoc');

        return is_array($row) && (bool)$row['has_permission'];
    }
}
