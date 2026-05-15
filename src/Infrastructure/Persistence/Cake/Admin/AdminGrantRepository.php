<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Cake\Admin;

use App\Domain\Admin\AdminGrant\Entity\GrantPermission;
use App\Domain\Admin\AdminGrant\Entity\GrantRolePermission;
use App\Domain\Admin\AdminGrant\Repository\AdminGrantRepository as DomainAdminGrantRepository;
use App\Domain\Admin\AdminGrant\ValueObject as Vo;
use DateTimeInterface;

final class AdminGrantRepository implements DomainAdminGrantRepository
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
     * 管理者権限の設定（ロール、個別を同時）
     *
     * @param \App\Domain\Admin\AdminGrant\ValueObject\AdminAccountId $adminAccountId
     * @param \App\Domain\Admin\AdminGrant\ValueObject\GrantRoleId[] $grantRoleIds
     * @param \App\Domain\Admin\AdminGrant\ValueObject\GrantPermissionId[] $grantPermissionIds
     * @return void
     */
    public function setAccountGrants(
        Vo\AdminAccountId $adminAccountId,
        array $grantRoleIds,
        array $grantPermissionIds,
    ): void {
        (new AdminGrantRepository\SaveAccountGrants(
            $adminAccountId,
            $grantRoleIds,
            $grantPermissionIds,
            $this->datetime,
        ))->run();
    }

    /**
     * ロール権限の作成
     *
     * @param \App\Domain\Admin\AdminGrant\Entity\GrantRolePermission $entity
     * @return \App\Domain\Admin\AdminGrant\Entity\GrantRolePermission
     */
    public function createRolePermission(GrantRolePermission $entity): GrantRolePermission
    {
        return (new AdminGrantRepository\CreateRolePermission($entity))->run();
    }

    /**
     * ロール権限の取得
     *
     * @param \App\Domain\Admin\AdminGrant\ValueObject\GrantRolePermissionId $id
     * @return \App\Domain\Admin\AdminGrant\Entity\GrantRolePermission
     */
    public function readRolePermission(Vo\GrantRolePermissionId $id): GrantRolePermission
    {
        return (new AdminGrantRepository\ReadRolePermission($id))->run();
    }

    /**
     * ロール権限の更新（ロールに紐づく権限を一括で再設定）
     *
     * @param \App\Domain\Admin\AdminGrant\ValueObject\GrantRoleId $grantRoleId
     * @param \App\Domain\Admin\AdminGrant\ValueObject\GrantPermissionId[] $grantPermissionIds
     * @return void
     */
    public function updateRolePermissions(
        Vo\GrantRoleId $grantRoleId,
        array $grantPermissionIds,
    ): void {
        (new AdminGrantRepository\UpdateRolePermissions(
            $grantRoleId,
            $grantPermissionIds,
            $this->datetime,
        ))->run();
    }

    /**
     * ロール権限の削除
     *
     * @param \App\Domain\Admin\AdminGrant\ValueObject\GrantRolePermissionId $id
     * @return \App\Domain\Admin\AdminGrant\Entity\GrantRolePermission
     */
    public function deleteRolePermission(Vo\GrantRolePermissionId $id): GrantRolePermission
    {
        return (new AdminGrantRepository\DeleteRolePermission($id))->run();
    }

    /**
     * 管理者権限の有無の判定
     *
     * @param \App\Domain\Admin\AdminGrant\ValueObject\AdminAccountId $adminAccountId
     * @param \App\Domain\Admin\AdminGrant\ValueObject\GrantPermissionId $grantPermissionId
     * @return bool
     */
    public function hasPermission(
        Vo\AdminAccountId $adminAccountId,
        Vo\GrantPermissionId $grantPermissionId,
    ): bool {
        return (new AdminGrantRepository\HasPermission(
            $adminAccountId,
            $grantPermissionId,
        ))->run();
    }

    /**
     * 管理者権限一覧の取得
     *
     * @param \App\Domain\Admin\AdminGrant\ValueObject\AdminAccountId $adminAccountId
     * @return \App\Domain\Admin\AdminGrant\Entity\GrantPermission[]
     */
    public function getAccountPermissions(Vo\AdminAccountId $adminAccountId): array
    {
        return (new AdminGrantRepository\GetAccountPermissions($adminAccountId))->run();
    }
}
