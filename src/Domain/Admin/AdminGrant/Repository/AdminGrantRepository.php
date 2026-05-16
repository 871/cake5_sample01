<?php
declare(strict_types=1);

namespace App\Domain\Admin\AdminGrant\Repository;

use App\Domain\Admin\AdminGrant\Entity\GrantPermission;
use App\Domain\Admin\AdminGrant\Entity\GrantRolePermission;
use App\Domain\Admin\AdminGrant\SearchCondition;
use App\Domain\Admin\AdminGrant\ValueObject as Vo;
use Cake\ORM\Query\SelectQuery;
use DateTimeInterface;

interface AdminGrantRepository
{
    /**
     * @param \DateTimeInterface $datetime
     */
    public function __construct(DateTimeInterface $datetime);

    /**
     * 管理者権限の設定（ロール、個別を同時）
     *
     * 指定した管理者アカウントのロール付与と個別権限付与を一括で設定する（既存設定は削除して再設定）
     *
     * @param \App\Domain\Admin\AdminGrant\ValueObject\AdminAccountId $adminAccountId
     * @param \App\Domain\Admin\AdminGrant\ValueObject\GrantRoleId[] $grantRoleIds
     * @param \App\Domain\Admin\AdminGrant\ValueObject\GrantPermissionId[] $grantPermissionIds
     * @return void
     */
    public function saveAccountGrants(
        Vo\AdminAccountId $adminAccountId,
        array $grantRoleIds,
        array $grantPermissionIds,
    ): void;

    /**
     * ロール権限の作成
     *
     * @param \App\Domain\Admin\AdminGrant\Entity\GrantRolePermission $entity
     * @return \App\Domain\Admin\AdminGrant\Entity\GrantRolePermission
     */
    public function createRolePermission(GrantRolePermission $entity): GrantRolePermission;

    /**
     * ロール権限の取得
     *
     * @param \App\Domain\Admin\AdminGrant\ValueObject\GrantRolePermissionId $id
     * @return \App\Domain\Admin\AdminGrant\Entity\GrantRolePermission
     */
    public function readRolePermission(Vo\GrantRolePermissionId $id): GrantRolePermission;

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
    ): void;

    /**
     * ロール権限の削除
     *
     * @param \App\Domain\Admin\AdminGrant\ValueObject\GrantRolePermissionId $id
     * @return \App\Domain\Admin\AdminGrant\Entity\GrantRolePermission
     */
    public function deleteRolePermission(Vo\GrantRolePermissionId $id): GrantRolePermission;

    /**
     * 管理者権限の有無の判定
     *
     * 指定した管理者アカウントが特定の権限を保持しているか判定する（ロール経由・個別付与を含む）
     *
     * @param \App\Domain\Admin\AdminGrant\ValueObject\AdminAccountId $adminAccountId
     * @param \App\Domain\Admin\AdminGrant\ValueObject\GrantPermissionId $grantPermissionId
     * @return bool
     */
    public function hasPermission(
        Vo\AdminAccountId $adminAccountId,
        Vo\GrantPermissionId $grantPermissionId,
    ): bool;

    /**
     * 管理者権限一覧の取得
     *
     * 指定した管理者アカウントが保持している権限一覧を取得する（ロール経由・個別付与を含む）
     *
     * @param \App\Domain\Admin\AdminGrant\ValueObject\AdminAccountId $adminAccountId
     * @return \App\Domain\Admin\AdminGrant\Entity\GrantPermission[]
     */
    public function getAccountPermissions(Vo\AdminAccountId $adminAccountId): array;

    /**
     * 管理者権限の検索
     *
     * アカウントと権限の組み合わせでユニークなレコードを検索する
     *
     * Memo: Cake5のController::paginate()の仕様を優先した設計とするため、Cake\ORM\Queryを直接返す形にしています。 --- IGNORE ---
     *
     * @param \App\Domain\Admin\AdminGrant\SearchCondition $condition
     * @return \Cake\ORM\Query\SelectQuery<\App\Model\Entity\Admin\AdminAccount>
     */
    public function search(SearchCondition $condition): SelectQuery;
}
