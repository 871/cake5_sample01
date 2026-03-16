<?php
declare(strict_types=1);

namespace App\Domain\Admin\AdminAccounts\Entity;

use App\Domain\Admin\AdminAccounts\ValueObject;

final class AdminAccount
{
    /**
     * @param ?string $id
     * @param ?string $login_id
     * @param ?string $name
     * @param ?string $email
     * @param ?string $password
     * @param ?string $role_id
     * @param ?string $status
     * @param ?string $role_name
     */
    public function __construct(
        private readonly ?string $id,
        private readonly ?string $login_id,
        private readonly ?string $name,
        private readonly ?string $email,
        private readonly ?string $password,
        private readonly ?string $role_id,
        private readonly ?string $status,
        private readonly ?string $role_name = null,
    ) {
    }

    /**
     * @return \App\Domain\Admin\AdminAccounts\ValueObject\Id
     */
    public function id(): ValueObject\Id
    {
        return ValueObject\Id::fromString($this->id);
    }

    /**
     * @return \App\Domain\Admin\AdminAccounts\ValueObject\LoginId
     */
    public function loginId(): ValueObject\LoginId
    {
        return ValueObject\LoginId::fromString($this->login_id);
    }

    /**
     * @return \App\Domain\Admin\AdminAccounts\ValueObject\Name
     */
    public function name(): ValueObject\Name
    {
        return ValueObject\Name::fromString($this->name);
    }

    /**
     * @return \App\Domain\Admin\AdminAccounts\ValueObject\Email
     */
    public function email(): ValueObject\Email
    {
        return ValueObject\Email::fromString($this->email);
    }

    /**
     * @return \App\Domain\Admin\AdminAccounts\ValueObject\Password
     */
    public function password(): ValueObject\Password
    {
        return ValueObject\Password::fromString($this->password);
    }

    /**
     * @return \App\Domain\Admin\AdminAccounts\ValueObject\RoleId
     */
    public function roleId(): ValueObject\RoleId
    {
        return ValueObject\RoleId::fromString($this->role_id);
    }

    /**
     * @return \App\Domain\Admin\AdminAccounts\ValueObject\Status
     */
    public function status(): ValueObject\Status
    {
        return ValueObject\Status::fromString($this->status);
    }

    /**
     * @return string
     */
    public function roleName(): string
    {
        return $this->role_name ?? '';
    }
}
