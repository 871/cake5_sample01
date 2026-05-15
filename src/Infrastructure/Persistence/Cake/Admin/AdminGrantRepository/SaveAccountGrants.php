<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Cake\Admin\AdminGrantRepository;

use App\Domain\Admin\AdminGrant\ValueObject\AdminAccountId;
use App\Domain\Admin\AdminGrant\ValueObject\GrantPermissionId;
use App\Domain\Admin\AdminGrant\ValueObject\GrantRoleId;
use App\Infrastructure\Persistence\Cake\Admin\AdminGrantMapper;
use App\Lib\UUID\UUID;
use App\Model\Table\Grant\GrantAccountPermissionsTable;
use App\Model\Table\Grant\GrantAccountRolesTable;
use Cake\ORM\Locator\LocatorAwareTrait;
use DateTimeInterface;

final class SaveAccountGrants
{
    use LocatorAwareTrait;

    /**
     * @var \App\Model\Table\Grant\GrantAccountRolesTable
     */
    private GrantAccountRolesTable $accountRolesTable;

    /**
     * @var \App\Model\Table\Grant\GrantAccountPermissionsTable
     */
    private GrantAccountPermissionsTable $accountPermissionsTable;

    /**
     * @param \App\Domain\Admin\AdminGrant\ValueObject\AdminAccountId $adminAccountId
     * @param \App\Domain\Admin\AdminGrant\ValueObject\GrantRoleId[] $grantRoleIds
     * @param \App\Domain\Admin\AdminGrant\ValueObject\GrantPermissionId[] $grantPermissionIds
     * @param \DateTimeInterface $datetime
     */
    public function __construct(
        private readonly AdminAccountId $adminAccountId,
        private readonly array $grantRoleIds,
        private readonly array $grantPermissionIds,
        private readonly DateTimeInterface $datetime,
    ) {
        $this->accountRolesTable = $this->fetchTable(GrantAccountRolesTable::class);
        $this->accountPermissionsTable = $this->fetchTable(GrantAccountPermissionsTable::class);
    }

    /**
     * @return void
     */
    public function run(): void
    {
        $accountId = $this->adminAccountId->toInt();
        $datetimeStr = $this->datetime->format('Y-m-d\TH:i:s');

        $this->accountRolesTable->getConnection()->transactional(function () use ($accountId, $datetimeStr): void {
            // 既存のロール付与を削除
            $this->accountRolesTable->deleteAll([
                'account_type' => AdminGrantMapper::ACCOUNT_TYPE,
                'account_id' => $accountId,
            ]);

            // 既存の個別権限付与を削除
            $this->accountPermissionsTable->deleteAll([
                'account_type' => AdminGrantMapper::ACCOUNT_TYPE,
                'account_id' => $accountId,
            ]);

            // ロールを付与
            $roleEntities = array_map(
                function (GrantRoleId $grantRoleId) use ($accountId, $datetimeStr) {
                    return $this->accountRolesTable->newEntity([
                        'id' => UUID::uuid7(),
                        'account_type' => AdminGrantMapper::ACCOUNT_TYPE,
                        'account_id' => $accountId,
                        'grant_role_id' => $grantRoleId->toInt(),
                        'created' => $datetimeStr,
                        'modified' => $datetimeStr,
                    ], ['validate' => false]);
                },
                $this->grantRoleIds,
            );
            $this->accountRolesTable->saveManyOrFail($roleEntities, ['checkExisting' => false]);

            // 個別権限を付与
            $permEntities = array_map(
                function (GrantPermissionId $grantPermissionId) use ($accountId, $datetimeStr) {
                    return $this->accountPermissionsTable->newEntity([
                        'id' => UUID::uuid7(),
                        'account_type' => AdminGrantMapper::ACCOUNT_TYPE,
                        'account_id' => $accountId,
                        'grant_permission_id' => $grantPermissionId->toInt(),
                        'created' => $datetimeStr,
                        'modified' => $datetimeStr,
                    ], ['validate' => false]);
                },
                $this->grantPermissionIds,
            );
            $this->accountPermissionsTable->saveManyOrFail($permEntities, ['checkExisting' => false]);
        });
    }
}
