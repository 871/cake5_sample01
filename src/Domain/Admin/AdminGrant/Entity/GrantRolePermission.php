<?php
declare(strict_types=1);

namespace App\Domain\Admin\AdminGrant\Entity;

use App\Domain\Admin\AdminGrant\ValueObject as Vo;
use App\Domain\Shared\ValueObject as SVo;

final class GrantRolePermission
{
    /**
     * @param \App\Domain\Admin\AdminGrant\ValueObject\GrantRolePermissionId $grant_role_permission_id
     * @param \App\Domain\Admin\AdminGrant\ValueObject\GrantRoleId $grant_role_id
     * @param \App\Domain\Admin\AdminGrant\ValueObject\GrantPermissionId $grant_permission_id
     * @param \App\Domain\Shared\ValueObject\Created $created
     * @param \App\Domain\Shared\ValueObject\Modified $modified
     * @param \App\Domain\Admin\AdminGrant\Entity\GrantPermission $grant_permissions
     */
    public function __construct(
        private readonly Vo\GrantRolePermissionId $grant_role_permission_id,
        private readonly Vo\GrantRoleId $grant_role_id,
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
    * @param \App\Domain\Admin\AdminGrant\ValueObject\GrantRoleId $grant_role_id
    * @return bool
    */
    public function hasGrantRoleId(Vo\GrantRoleId $grant_role_id): bool
    {
        return $this->grant_role_id->toString() === $grant_role_id->toString();
    }

    /**
     * @return \App\Domain\Admin\AdminGrant\ValueObject\GrantRolePermissionId
     */
    public function grantRolePermissionId(): Vo\GrantRolePermissionId
    {
        return $this->grant_role_permission_id;
    }

    /**
     * @return \App\Domain\Admin\AdminGrant\ValueObject\GrantRoleId
     */
    public function grantRoleId(): Vo\GrantRoleId
    {
        return $this->grant_role_id;
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

    /**
     * @return \App\Domain\Admin\AdminGrant\Entity\GrantPermission
     */
    public function grantPermissions(): GrantPermission
    {
        return $this->grant_permissions;
    }
}
