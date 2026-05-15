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
    WHERE `account_type` = ?
      AND `account_id` = ?
      AND `grant_permission_id` = ?
    UNION ALL
    SELECT 1 FROM `grant_account_roles` AS `gar`
    INNER JOIN `grant_role_permissions` AS `grp`
        ON `gar`.`grant_role_id` = `grp`.`grant_role_id`
    WHERE `gar`.`account_type` = ?
      AND `gar`.`account_id` = ?
      AND `grp`.`account_type` = ?
      AND `grp`.`grant_permission_id` = ?
    LIMIT 1
) AS `has_permission`
SQL;

        $stmt = $this->accountPermissionsTable->getConnection()->execute($sql, [
            AdminGrantMapper::ACCOUNT_TYPE,
            $accountId,
            $permissionId,
            AdminGrantMapper::ACCOUNT_TYPE,
            $accountId,
            AdminGrantMapper::ACCOUNT_TYPE,
            $permissionId,
        ]);

        /** @var array<string, mixed>|false $row */
        $row = $stmt->fetch('assoc');

        return is_array($row) && (bool)$row['has_permission'];
    }
}
