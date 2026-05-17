<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Cake\Admin\AdminGrantRepository;

use App\Domain\Admin\AdminGrant\ValueObject\GrantPermissionId;
use App\Domain\Admin\AdminGrant\ValueObject\GrantRoleId;
use App\Infrastructure\Persistence\Cake\Admin\AdminGrantMapper;
use App\Lib\UUID\UUID;
use App\Model\Table\Grant\GrantRolePermissionsTable;
use Cake\ORM\Locator\LocatorAwareTrait;
use DateTimeInterface;

final class UpdateRolePermissions
{
    use LocatorAwareTrait;

    /**
     * @var \App\Model\Table\Grant\GrantRolePermissionsTable
     */
    private GrantRolePermissionsTable $table;

    /**
     * @param \App\Domain\Admin\AdminGrant\ValueObject\GrantRoleId $grantRoleId
     * @param \App\Domain\Admin\AdminGrant\ValueObject\GrantPermissionId[] $grantPermissionIds
     * @param \DateTimeInterface $datetime
     */
    public function __construct(
        private readonly GrantRoleId $grantRoleId,
        private readonly array $grantPermissionIds,
        private readonly DateTimeInterface $datetime,
    ) {
        $this->table = $this->fetchTable(GrantRolePermissionsTable::class);
    }

    /**
     * @return void
     */
    public function run(): void
    {
        $roleId = $this->grantRoleId->toInt();
        $datetimeStr = $this->datetime->format('Y-m-d\TH:i:s');

        $this->table->getConnection()->transactional(function () use ($roleId, $datetimeStr): void {
            // 既存のロール権限を削除
            $this->table->deleteAll([
                'account_type' => AdminGrantMapper::ACCOUNT_TYPE,
                'grant_role_id' => $roleId,
            ]);

            if ($this->grantPermissionIds === []) {
                return;
            }

            // 新しいロール権限を登録
            $entities = array_map(
                function (GrantPermissionId $grantPermissionId) use ($roleId, $datetimeStr) {
                    return $this->table->newEntity([
                        'id' => UUID::uuid7(),
                        'account_type' => AdminGrantMapper::ACCOUNT_TYPE,
                        'grant_role_id' => $roleId,
                        'grant_permission_id' => $grantPermissionId->toInt(),
                        'created' => $datetimeStr,
                        'modified' => $datetimeStr,
                    ], ['validate' => false]);
                },
                $this->grantPermissionIds,
            );
            $this->table->saveManyOrFail($entities, ['checkExisting' => false]);
        });
    }
}
