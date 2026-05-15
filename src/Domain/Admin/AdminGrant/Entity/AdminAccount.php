<?php
declare(strict_types=1);

namespace App\Domain\Admin\AdminGrant\Entity;

use App\Domain\Admin\AdminGrant\ValueObject as Vo;

final class AdminAccount
{
    /**
     * @param ?string $id
     * @param ?string $email
     * @param ?string $name
     * @param ?string $admin_note
     * @param ?string $account_status_master_id
     * @param ?string $account_status_master_name
     * @param ?string $account_status_master_code
     * @param ?string $grant_permission_name
     * @param ?string $grant_permission_code
     */
    public function __construct(
        private readonly ?string $id,
        private readonly ?string $email,
        private readonly ?string $name,
        private readonly ?string $admin_note,
        private readonly ?string $account_status_master_id,
        private readonly ?string $account_status_master_name,
        private readonly ?string $account_status_master_code,
        private readonly ?string $grant_permission_name,
        private readonly ?string $grant_permission_code,
    ) {
    }

    /**
     * @return \App\Domain\Admin\AdminGrant\ValueObject\AdminAccountId
     */
    public function id(): Vo\AdminAccountId
    {
        return new Vo\AdminAccountId($this->id);
    }

    /**
     * @return \App\Domain\Admin\AdminGrant\ValueObject\Email
     */
    public function email(): Vo\Email
    {
        return new Vo\Email($this->email);
    }

    /**
     * @return \App\Domain\Admin\AdminGrant\ValueObject\Name
     */
    public function name(): Vo\Name
    {
        return new Vo\Name($this->name);
    }

    /**
     * @return \App\Domain\Admin\AdminGrant\ValueObject\AdminNote
     */
    public function adminNote(): Vo\AdminNote
    {
        return new Vo\AdminNote($this->admin_note);
    }

    /**
     * @return \App\Domain\Admin\AdminGrant\ValueObject\AccountStatusMasterId
     */
    public function accountStatusMasterId(): Vo\AccountStatusMasterId
    {
        return new Vo\AccountStatusMasterId($this->account_status_master_id);
    }

    /**
     * @return \App\Domain\Admin\AdminGrant\ValueObject\AccountStatusMasterName
     */
    public function accountStatusMasterName(): Vo\AccountStatusMasterName
    {
        return new Vo\AccountStatusMasterName($this->account_status_master_name);
    }

    /**
     * @return \App\Domain\Admin\AdminGrant\ValueObject\AccountStatusMasterCode
     */
    public function accountStatusMasterCode(): Vo\AccountStatusMasterCode
    {
        return new Vo\AccountStatusMasterCode($this->account_status_master_code);
    }

    /**
     * @return \App\Domain\Admin\AdminGrant\ValueObject\Name
     */
    public function grantPermissionName(): Vo\Name
    {
        return new Vo\Name($this->grant_permission_name);
    }

    /**
     * @return \App\Domain\Admin\AdminGrant\ValueObject\Code
     */
    public function grantPermissionCode(): Vo\Code
    {
        return new Vo\Code($this->grant_permission_code);
    }
}
