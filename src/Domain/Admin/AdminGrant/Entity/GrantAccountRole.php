<?php
declare(strict_types=1);

namespace App\Domain\Admin\AdminGrant\Entity;

use App\Domain\Admin\AdminGrant\ValueObject as Vo;
use App\Domain\Shared\ValueObject as SVo;

final class GrantAccountRole
{
    /**
     * @param ?string $id
     * @param ?string $account_id
     * @param ?string $grant_role_id
     * @param ?string $created
     * @param ?string $modified
     */
    public function __construct(
        private readonly ?string $id,
        private readonly ?string $account_id,
        private readonly ?string $grant_role_id,
        private readonly ?string $created,
        private readonly ?string $modified,
    ) {
    }

    /**
     * @return \App\Domain\Admin\AdminGrant\ValueObject\GrantAccountRoleId
     */
    public function id(): Vo\GrantAccountRoleId
    {
        return new Vo\GrantAccountRoleId($this->id);
    }

    /**
     * @return \App\Domain\Admin\AdminGrant\ValueObject\AdminAccountId
     */
    public function accountId(): Vo\AdminAccountId
    {
        return new Vo\AdminAccountId($this->account_id);
    }

    /**
     * @return \App\Domain\Admin\AdminGrant\ValueObject\GrantRoleId
     */
    public function grantRoleId(): Vo\GrantRoleId
    {
        return new Vo\GrantRoleId($this->grant_role_id);
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
