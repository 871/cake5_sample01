<?php
declare(strict_types=1);

namespace App\Domain\Admin\AdminGrant\Entity;

use App\Domain\Admin\AdminGrant\ValueObject as Vo;
use App\Domain\Shared\ValueObject as SVo;

final class GrantAccountPermission
{
    /**
     * @param Vo\GrantAccountPermissionId $grant_account_permission_id
     * @param Vo\AdminAccountId $admin_account_id
     * @param Vo\GrantPermissionId $grant_permission_id
     * @param SVo\Created $created
     * @param SVo\Modified $modified
     */
    public function __construct(
        private readonly Vo\GrantAccountPermissionId $grant_account_permission_id,
        private readonly Vo\AdminAccountId $admin_account_id,
        private readonly Vo\GrantPermissionId $grant_permission_id,
        private readonly SVo\Created $created,
        private readonly SVo\Modified $modified,
        private readonly GrantPermission $grant_permissions,
    ) {
        if (!$this->grant_permissions->hasGrantPermissionId($this->grant_permission_id)) {
            throw new \DomainException('GrantPermission id does not match grant_permission_id');
        }
    }

    /**
     * @param Vo\AdminAccountId $admin_account_id
     * @return bool
     */
    public function hasAdminAccountId(Vo\AdminAccountId $admin_account_id): bool
    {
        return $this->admin_account_id->toString() === $admin_account_id->toString();
    }

    /**
     * @return \App\Domain\Admin\AdminGrant\ValueObject\GrantAccountPermissionId
     */
    public function grantAccountPermissionId(): Vo\GrantAccountPermissionId
    {
        return $this->grant_account_permission_id;
    }

    /**
     * @return \App\Domain\Admin\AdminGrant\ValueObject\AdminAccountId
     */
    public function adminAccountId(): Vo\AdminAccountId
    {
        return $this->admin_account_id;
    }

    /**
     * @return \App\Domain\Admin\AdminGrant\ValueObject\GrantPermissionId
     */
    public function grantPermissionId(): Vo\GrantPermissionId
    {
        return $this->grant_permission_id;
    }

    /**
     * @return \App\Domain\Shared\ValueObject\Created
     */
    public function created(): SVo\Created
    {
        return $this->created;
    }

    /**
     * @return \App\Domain\Shared\ValueObject\Modified
     */
    public function modified(): SVo\Modified
    {
        return $this->modified;
    }
}
