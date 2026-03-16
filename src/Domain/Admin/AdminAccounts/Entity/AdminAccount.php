<?php
declare(strict_types=1);

namespace App\Domain\Admin\AdminAccounts\Entity;

use App\Domain\Admin\AdminAccounts\ValueObject;

final class AdminAccount
{
    /**
     * @param ?string $id
     * @param ?string $email
     * @param ?string $password
     * @param ?string $name
     * @param ?string $admin_note
     * @param ?string $account_status_master_id
     * @param ?string $is_email_verified
     * @param ?string $password_changed_at
     * @param ?string $password_expires_at
     */
    public function __construct(
        private readonly ?string $id,
        private readonly ?string $email,
        private readonly ?string $password,
        private readonly ?string $name,
        private readonly ?string $admin_note,
        private readonly ?string $account_status_master_id,
        private readonly ?string $is_email_verified,
        private readonly ?string $password_changed_at,
        private readonly ?string $password_expires_at,
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
     * @return \App\Domain\Admin\AdminAccounts\ValueObject\Name
     */
    public function name(): ValueObject\Name
    {
        return ValueObject\Name::fromString($this->name);
    }

    /**
     * @return \App\Domain\Admin\AdminAccounts\ValueObject\AdminNote
     */
    public function adminNote(): ValueObject\AdminNote
    {
        return ValueObject\AdminNote::fromString($this->admin_note);
    }

    /**
     * @return \App\Domain\Admin\AdminAccounts\ValueObject\AccountStatusMasterId
     */
    public function accountStatusMasterId(): ValueObject\AccountStatusMasterId
    {
        return new ValueObject\AccountStatusMasterId($this->account_status_master_id);
    }

    /**
     * @return \App\Domain\Admin\AdminAccounts\ValueObject\IsEmailVerified
     */
    public function isEmailVerified(): ValueObject\IsEmailVerified
    {
        return new ValueObject\IsEmailVerified($this->is_email_verified);
    }

    /**
     * @return \App\Domain\Admin\AdminAccounts\ValueObject\PasswordChangedAt
     */
    public function passwordChangedAt(): ValueObject\PasswordChangedAt
    {
        return new ValueObject\PasswordChangedAt($this->password_changed_at);
    }

    /**
     * @return \App\Domain\Admin\AdminAccounts\ValueObject\PasswordExpiresAt
     */
    public function passwordExpiresAt(): ValueObject\PasswordExpiresAt
    {
        return new ValueObject\PasswordExpiresAt($this->password_expires_at);
    }
}
