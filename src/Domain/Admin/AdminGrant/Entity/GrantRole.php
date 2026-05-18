<?php
declare(strict_types=1);

namespace App\Domain\Admin\AdminGrant\Entity;

use App\Domain\Admin\AdminGrant\ValueObject as Vo;
use App\Domain\Shared\ValueObject as SVo;

final class GrantRole
{
    /**
     * @param \App\Domain\Admin\AdminGrant\ValueObject\GrantRoleId $grant_role_id
     * @param \App\Domain\Admin\AdminGrant\ValueObject\Code $code
     * @param \App\Domain\Admin\AdminGrant\ValueObject\Name $name
     * @param \App\Domain\Admin\AdminGrant\ValueObject\Description $description
     * @param \App\Domain\Admin\AdminGrant\ValueObject\Sort $sort
     * @param \App\Domain\Admin\AdminGrant\ValueObject\IsActive $is_active
     * @param \App\Domain\Shared\ValueObject\Created $created
     * @param \App\Domain\Shared\ValueObject\Modified $modified
     * @param array<\App\Domain\Admin\AdminGrant\Entity\GrantRolePermission> $grant_role_permissions
     */
    public function __construct(
        private readonly Vo\GrantRoleId $grant_role_id,
        private readonly Vo\Code $code,
        private readonly Vo\Name $name,
        private readonly Vo\Description $description,
        private readonly Vo\Sort $sort,
        private readonly Vo\IsActive $is_active,
        private readonly SVo\Created $created,
        private readonly SVo\Modified $modified,
        private readonly array $grant_role_permissions = [],
    ) {
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
     * @return \App\Domain\Admin\AdminGrant\ValueObject\GrantRoleId
     */
    public function grantRoleId(): Vo\GrantRoleId
    {
        return $this->grant_role_id;
    }

    /**
     * @return \App\Domain\Admin\AdminGrant\ValueObject\Code
     */
    public function code(): Vo\Code
    {
        return $this->code;
    }

    /**
     * @return \App\Domain\Admin\AdminGrant\ValueObject\Name
     */
    public function name(): Vo\Name
    {
        return $this->name;
    }

    /**
     * @return \App\Domain\Admin\AdminGrant\ValueObject\Description
     */
    public function description(): Vo\Description
    {
        return $this->description;
    }

    /**
     * @return \App\Domain\Admin\AdminGrant\ValueObject\Sort
     */
    public function sort(): Vo\Sort
    {
        return $this->sort;
    }

    /**
     * @return \App\Domain\Admin\AdminGrant\ValueObject\IsActive
     */
    public function isActive(): Vo\IsActive
    {
        return $this->is_active;
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
     * @return array<\App\Domain\Admin\AdminGrant\Entity\GrantRolePermission>
     */
    public function grantRolePermissions(): array
    {
        return $this->grant_role_permissions;
    }
}
