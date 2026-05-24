<?php
declare(strict_types=1);

namespace App\Test\TestCase\Domain\Admin\AdminGrant;

use App\Domain\Admin\AdminGrant\Entity\AdminAccountGrant;
use App\Domain\Admin\AdminGrant\Entity\GrantAccountRole;
use App\Domain\Admin\AdminGrant\ValueObject\AccountStatusMasterCode;
use App\Domain\Admin\AdminGrant\ValueObject\AccountStatusMasterId;
use App\Domain\Admin\AdminGrant\ValueObject\AccountStatusMasterName;
use App\Domain\Admin\AdminGrant\ValueObject\AdminAccountId;
use App\Domain\Admin\AdminGrant\ValueObject\AdminNote;
use App\Domain\Admin\AdminGrant\ValueObject\Email;
use App\Domain\Admin\AdminGrant\ValueObject\GrantAccountRoleId;
use App\Domain\Admin\AdminGrant\ValueObject\GrantRoleId;
use App\Domain\Admin\AdminGrant\ValueObject\Name;
use App\Domain\Shared\ValueObject\Created;
use App\Domain\Shared\ValueObject\Modified;
use PHPUnit\Framework\TestCase;

final class GrantAccountRoleTest extends TestCase
{
    public function testCanReadAssignedAdminAccountGrant(): void
    {
        $adminAccountGrant = new AdminAccountGrant(
            admin_account_id: new AdminAccountId('1'),
            email: new Email('admin@example.com'),
            name: new Name('管理者'),
            admin_note: new AdminNote('note'),
            account_status_master_id: new AccountStatusMasterId('1'),
            account_status_master_code: new AccountStatusMasterCode('ACTIVE'),
            account_status_master_name: new AccountStatusMasterName('有効'),
            grant_account_roles: [],
            grant_account_permissions: [],
        );

        $grantAccountRole = new GrantAccountRole(
            grant_account_role_id: new GrantAccountRoleId('00000000-0000-4000-8000-000000000001'),
            admin_account_id: new AdminAccountId('1'),
            grant_role_id: new GrantRoleId('10'),
            created: new Created('2026-05-22T00:00:00'),
            modified: new Modified('2026-05-22T00:00:00'),
            admin_account_grant: $adminAccountGrant,
            grant_role: null,
        );

        $this->assertSame($adminAccountGrant, $grantAccountRole->adminAccountGrant());
        $this->assertSame('管理者', $grantAccountRole->adminAccountGrant()->name()->toString());
    }
}
