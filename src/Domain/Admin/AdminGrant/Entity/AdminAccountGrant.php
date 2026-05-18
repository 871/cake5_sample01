<?php
declare(strict_types=1);

namespace App\Domain\Admin\AdminGrant\Entity;

use App\Domain\Admin\AdminGrant\ValueObject as Vo;

final class AdminAccountGrant
{
    /**
     * @param array<\App\Domain\Admin\AdminGrant\Entity\GrantPermission> $grant_permissions
     */
    private readonly array $grant_permissions;

    /**
     * @param Vo\AdminAccountId $admin_account_id
     * @param Vo\Email $email
     * @param Vo\Name $name
     * @param Vo\AdminNote $admin_note
     * @param Vo\AccountStatusMasterId $account_status_master_id
     * @param Vo\AccountStatusMasterCode $admin_account_status_master_code
     * @param Vo\AccountStatusMasterName $admin_account_status_master_name
     * @param array $grant_account_roles<App\Domain\Admin\AdminGrant\Entity\GrantAccountRole>
     * @param array $grant_account_permissions<App\Domain\Admin\AdminGrant\Entity\GrantAccountPermission>
     */
    public function __construct(
        private readonly Vo\AdminAccountId $admin_account_id,
        private readonly Vo\Email $email,
        private readonly Vo\Name $name,
        private readonly Vo\AdminNote $admin_note,
        private readonly Vo\AccountStatusMasterId $account_status_master_id,
        private readonly Vo\AccountStatusMasterCode $admin_account_status_master_code,
        private readonly Vo\AccountStatusMasterName $admin_account_status_master_name,
        private readonly array $grant_account_roles = [],
        private readonly array $grant_account_permissions = [],
    ) {
        foreach ($this->grant_account_roles as $grant_role) {
            if (!$grant_role->hasAdminAccountId($this->admin_account_id)) {
                throw new \DomainException('GrantAccountRole admin_account_id does not match AdminAccountId');
            }
        }

        foreach ($this->grant_account_permissions as $grant_account_permission) {
            if (!$grant_account_permission->hasAdminAccountId($this->admin_account_id)) {
                throw new \DomainException('GrantAccountPermission admin_account_id does not match AdminAccountId');
            }
        }
    }

    /**
     * @param \App\Domain\Admin\AdminGrant\Entity\GrantAccountRole $grant_account_role
     * @return void
     */
    public function assignAddGrantAccountRole(GrantAccountRole $grant_account_role): void
    {
        if (!$grant_account_role->hasAdminAccountId($this->admin_account_id)) {
            throw new \DomainException('GrantAccountRole admin_account_id does not match AdminAccountId');
        }

        $this->grant_account_roles[] = $grant_account_role;
    }

    /**
     * @param \App\Domain\Admin\AdminGrant\Entity\GrantAccountPermission $grant_account_permission
     * @return void
     */
    public function assignAddGrantAccountPermission(GrantAccountPermission $grant_account_permission): void
    {
        if (!$grant_account_permission->hasAdminAccountId($this->admin_account_id)) {
            throw new \DomainException('GrantAccountPermission admin_account_id does not match AdminAccountId');
        }

        $this->grant_account_permissions[] = $grant_account_permission;
    }

    /**
     * @param \App\Domain\Admin\AdminGrant\ValueObject\AdminAccountId $id
     * @return bool
     */
    public function hasAdminAccountId(Vo\AdminAccountId $id): bool
    {
        return $this->admin_account_id->toString() === $id->toString();
    }

    /**
     * @return \App\Domain\Admin\AdminGrant\ValueObject\AdminAccountId
     */
    public function adminAccountId(): Vo\AdminAccountId
    {
        return $this->admin_account_id;
    }

    /**
     * @return \App\Domain\Admin\AdminGrant\ValueObject\Email
     */
    public function email(): Vo\Email
    {
        return $this->email;
    }

    /**
     * @return \App\Domain\Admin\AdminGrant\ValueObject\Name
     */
    public function name(): Vo\Name
    {
        return $this->name;
    }

    /**
     * @return \App\Domain\Admin\AdminGrant\ValueObject\AdminNote
     */
    public function adminNote(): Vo\AdminNote
    {
        return $this->admin_note;
    }

    /**
     * @return \App\Domain\Admin\AdminGrant\ValueObject\AccountStatusMasterId
     */
    public function accountStatusMasterId(): Vo\AccountStatusMasterId
    {
        return $this->account_status_master_id;
    }

    /**
     * @return \App\Domain\Admin\AdminGrant\ValueObject\AccountStatusMasterCode
     */
    public function adminAccountStatusMasterCode(): Vo\AccountStatusMasterCode
    {
        return $this->admin_account_status_master_code;
    }

    /**
     * @return \App\Domain\Admin\AdminGrant\ValueObject\AccountStatusMasterName
     */
    public function adminAccountStatusMasterName(): Vo\AccountStatusMasterName
    {
        return $this->admin_account_status_master_name;
    }

    /**
     * @return array<\App\Domain\Admin\AdminGrant\Entity\GrantAccountRole>
     */
    public function grantAccountRoles(): array
    {
        return $this->grant_account_roles;
    }

    /**
     * @return array<\App\Domain\Admin\AdminGrant\Entity\GrantAccountPermission>
     */
    public function grantAccountPermissions(): array
    {
        return $this->grant_account_permissions;
    }
}
