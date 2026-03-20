<?php
declare(strict_types=1);

namespace App\Domain\Admin\AdminAccounts\Entity;

use App\Domain\Admin\AdminAccounts\ValueObject as Vo;
use App\Domain\Shared\ValueObject as SVo;

final class AdminAccount
{
    /**
     * @param ?string $id
     * @param ?string $email
     * @param ?string $password
     * @param ?string $name
     * @param ?string $admin_note
     * @param ?string $account_status_master_id
     * @param ?string $account_status_master_code
     * @param ?string $account_status_master_name
     * @param ?string $is_email_verified
     * @param ?string $password_changed_at
     * @param ?string $password_expires_at
     * @param ?string $created
     * @param ?string $created_by
     * @param ?string $created_ip
     * @param ?string $modified
     * @param ?string $modified_by
     * @param ?string $modified_ip
     */
    public function __construct(
        private readonly ?string $id,
        private readonly ?string $email,
        private readonly ?string $password,
        private readonly ?string $name,
        private readonly ?string $admin_note,
        private readonly ?string $account_status_master_id,
        private readonly ?string $account_status_master_code,
        private readonly ?string $account_status_master_name,
        private readonly ?string $is_email_verified,
        private readonly ?string $password_changed_at,
        private readonly ?string $password_expires_at,
        private readonly ?string $created,
        private readonly ?string $created_by,
        private readonly ?string $created_ip,
        private readonly ?string $modified,
        private readonly ?string $modified_by,
        private readonly ?string $modified_ip,
    ) {
    }

    /**
     * @return \App\Domain\Admin\AdminAccounts\ValueObject\Id
     */
    public function id(): Vo\Id
    {
        return Vo\Id::fromString($this->id);
    }

    /**
     * @return \App\Domain\Admin\AdminAccounts\ValueObject\Email
     */
    public function email(): Vo\Email
    {
        return Vo\Email::fromString($this->email);
    }

    /**
     * @return \App\Domain\Admin\AdminAccounts\ValueObject\Password
     */
    public function password(): Vo\Password
    {
        return Vo\Password::fromString($this->password);
    }

    /**
     * @return \App\Domain\Admin\AdminAccounts\ValueObject\Name
     */
    public function name(): Vo\Name
    {
        return Vo\Name::fromString($this->name);
    }

    /**
     * @return \App\Domain\Admin\AdminAccounts\ValueObject\AdminNote
     */
    public function adminNote(): Vo\AdminNote
    {
        return Vo\AdminNote::fromString($this->admin_note);
    }

    /**
     * @return \App\Domain\Admin\AdminAccounts\ValueObject\AccountStatusMasterId
     */
    public function accountStatusMasterId(): Vo\AccountStatusMasterId
    {
        return new Vo\AccountStatusMasterId($this->account_status_master_id);
    }

    /**
     * @return \App\Domain\Admin\AdminAccounts\ValueObject\AccountStatusMasterCode
     */
    public function accountStatusMasterCode(): Vo\AccountStatusMasterCode
    {
        return new Vo\AccountStatusMasterCode($this->account_status_master_code);
    }

    /**
     * @return \App\Domain\Admin\AdminAccounts\ValueObject\AccountStatusMasterName
     */
    public function accountStatusMasterName(): Vo\AccountStatusMasterName
    {
        return new Vo\AccountStatusMasterName($this->account_status_master_name);
    }

    /**
     * @return \App\Domain\Admin\AdminAccounts\ValueObject\IsEmailVerified
     */
    public function isEmailVerified(): Vo\IsEmailVerified
    {
        return new Vo\IsEmailVerified($this->is_email_verified);
    }

    /**
     * @return \App\Domain\Admin\AdminAccounts\ValueObject\PasswordChangedAt
     */
    public function passwordChangedAt(): Vo\PasswordChangedAt
    {
        return new Vo\PasswordChangedAt($this->password_changed_at);
    }

    /**
     * @return \App\Domain\Admin\AdminAccounts\ValueObject\PasswordExpiresAt
     */
    public function passwordExpiresAt(): Vo\PasswordExpiresAt
    {
        return new Vo\PasswordExpiresAt($this->password_expires_at);
    }

    /**
     * @return \App\Domain\Shared\ValueObject\Created
     */
    public function created(): SVo\Created
    {
        return new SVo\Created($this->created);
    }

    /**
     * @return \App\Domain\Shared\ValueObject\CreatedBy
     */
    public function createdBy(): SVo\CreatedBy
    {
        return new SVo\CreatedBy($this->created_by);
    }

    /**
     * @return \App\Domain\Shared\ValueObject\CreatedIp
     */
    public function createdIp(): SVo\CreatedIp
    {
        return new SVo\CreatedIp($this->created_ip);
    }

    /**
     * @return \App\Domain\Shared\ValueObject\Modified
     */
    public function modified(): SVo\Modified
    {
        return new SVo\Modified($this->modified);
    }

    /**
     * @return \App\Domain\Shared\ValueObject\ModifiedBy
     */
    public function modifiedBy(): SVo\ModifiedBy
    {
        return new SVo\ModifiedBy($this->modified_by);
    }

    /**
     * @return \App\Domain\Shared\ValueObject\ModifiedIp
     */
    public function modifiedIp(): SVo\ModifiedIp
    {
        return new SVo\ModifiedIp($this->modified_ip);
    }
}
