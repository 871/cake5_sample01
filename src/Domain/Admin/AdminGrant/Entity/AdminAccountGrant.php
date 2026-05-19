<?php
declare(strict_types=1);

namespace App\Domain\Admin\AdminGrant\Entity;

use App\Domain\Admin\AdminGrant\ValueObject as Vo;

final class AdminAccountGrant
{
    /**
     * @param Vo\AdminAccountId $admin_account_id
     * @param Vo\Email $email
     * @param Vo\Name $name
     * @param Vo\AdminNote $admin_note
     * @param Vo\AccountStatusMasterId $account_status_master_id
     * @param Vo\AccountStatusMasterCode $account_status_master_code
     * @param Vo\AccountStatusMasterName $account_status_master_name
     * @param array $grant_account_roles<App\Domain\Admin\AdminGrant\Entity\GrantAccountRole>
     * @param array $grant_account_permissions<App\Domain\Admin\AdminGrant\Entity\GrantAccountPermission>
     */
    public function __construct(
        private readonly Vo\AdminAccountId $admin_account_id,
        private readonly Vo\Email $email,
        private readonly Vo\Name $name,
        private readonly Vo\AdminNote $admin_note,
        private readonly Vo\AccountStatusMasterId $account_status_master_id,
        private readonly Vo\AccountStatusMasterCode $account_status_master_code,
        private readonly Vo\AccountStatusMasterName $account_status_master_name,
        private array $grant_account_roles = [],
        private array $grant_account_permissions = [],
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
     * @param array<\App\Domain\Admin\AdminGrant\Entity\GrantAccountRole> $grant_account_roles
     * @return self
     */
    public function assignGrantAccountRoles(array $grant_account_roles): self
    {
        foreach ($grant_account_roles as $grant_account_role) {
            if (!$grant_account_role->hasAdminAccountId($this->admin_account_id)) {
                throw new \DomainException('GrantAccountRole admin_account_id does not match AdminAccountId');
            }
        }

        $ther = clone $this;
        $ther->grant_account_roles = $grant_account_roles;

        return $ther;
    }

    /**
     * @param array<\App\Domain\Admin\AdminGrant\Entity\GrantAccountPermission> $grant_account_permissions
     * @return self
     */
    public function assignGrantAccountPermissions(array $grant_account_permissions): self
    {
        foreach ($grant_account_permissions as $grant_account_permission) {
            if (!$grant_account_permission->hasAdminAccountId($this->admin_account_id)) {
                throw new \DomainException('GrantAccountPermission admin_account_id does not match AdminAccountId');
            }
        }

        $ther = clone $this;
        $ther->grant_account_permissions = $grant_account_permissions;

        return $ther;
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
     * @param \App\Domain\Admin\AdminGrant\ValueObject\Code $code
     */
    public function hasPermissionCode(Vo\Code $code): bool
    {
        return array_filter($this->grant_account_permissions, function($grant_account_permission) use ($code) {
            return $grant_account_permission->hasPermissionCode($code);
        }) !== []
        ||
        array_filter($this->grant_account_roles, function($grant_account_role) use ($code) {
            return $grant_account_role->hasPermissionCode($code);
        }) !== [];
    }

    /**
     * 
     * @param \App\Domain\Admin\AdminGrant\ValueObject\Code $code
     * @return array<string>
     */
    public function grantSettings(Vo\Code $code): array
    {
        return array_filter([
            array_filter($this->grant_account_permissions, function($grant_account_permission) use ($code) {
                return $grant_account_permission->hasPermissionCode($code);
            }) !== [] ? 'アカウント付与': null,

            ...array_map(function($grant_account_role) use ($code) {
                return $grant_account_role->hasPermissionCode($code) 
                    ? $grant_account_role->grantRole()->name()
                    : null;
            }, $this->grant_account_roles),
        ], fn($v) => $v !== null);
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
    public function accountStatusMasterCode(): Vo\AccountStatusMasterCode
    {
        return $this->account_status_master_code;
    }

    /**
     * @return \App\Domain\Admin\AdminGrant\ValueObject\AccountStatusMasterName
     */
    public function accountStatusMasterName(): Vo\AccountStatusMasterName
    {
        return $this->account_status_master_name;
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
