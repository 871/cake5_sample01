<?php
declare(strict_types=1);

namespace App\Domain\Admin\AdminGrant\Entity;

use App\Domain\Admin\AdminGrant\ValueObject as Vo;
use App\Domain\Shared\ValueObject as SVo;

final class GrantAccountPermission
{
    /**
     * @param ?string $id
     * @param ?string $account_id
     * @param ?string $grant_permission_id
     * @param ?string $created
     * @param ?string $modified
     */
    public function __construct(
        private readonly ?string $id,
        private readonly ?string $account_id,
        private readonly ?string $grant_permission_id,
        private readonly ?string $created,
        private readonly ?string $modified,
    ) {
    }

    /**
     * @return \App\Domain\Admin\AdminGrant\ValueObject\GrantAccountPermissionId
     */
    public function id(): Vo\GrantAccountPermissionId
    {
        return new Vo\GrantAccountPermissionId($this->id);
    }

    /**
     * @return \App\Domain\Admin\AdminGrant\ValueObject\AdminAccountId
     */
    public function accountId(): Vo\AdminAccountId
    {
        return new Vo\AdminAccountId($this->account_id);
    }

    /**
     * @return \App\Domain\Admin\AdminGrant\ValueObject\GrantPermissionId
     */
    public function grantPermissionId(): Vo\GrantPermissionId
    {
        return new Vo\GrantPermissionId($this->grant_permission_id);
    }

    /**
     * @return \App\Domain\Shared\ValueObject\Created
     */
    public function created(): SVo\Created
    {
        return new SVo\Created($this->created);
    }

    /**
     * @return \App\Domain\Shared\ValueObject\Modified
     */
    public function modified(): SVo\Modified
    {
        return new SVo\Modified($this->modified);
    }
}
