<?php
declare(strict_types=1);

namespace App\View\Helper;

use App\Domain\Admin\AdminGrant\ValueObject\Code;
use App\Domain\Admin\AdminGrant\ValueObject\AdminAccountId;
use App\Domain\Shared\Enum as SEn;
use App\Infrastructure\Persistence\Cake\Admin\AdminGrant\AdminAccountGrantRepository;
use App\Security\Input\Cast;
use App\Model\Table\Grant\GrantPermissionsTable;
use Cake\ORM\Locator\LocatorAwareTrait;
use Cake\View\Helper;
use DateTimeImmutable;

class AdminGrantHelper extends Helper
{
    use LocatorAwareTrait;

    /**
     * @param string|int $permissionId
     * @return bool
     */
    public function hasPermission(int|string $permissionId): bool
    {
        $accountId = Cast::toStringOrNull($this->getView()->getRequest()->getParam('account_id'));
        if ($accountId === null || $accountId === '') {
            return false;
        }

        /** @var \App\Model\Table\Grant\GrantPermissionsTable $table */
        $table = $this->fetchTable(GrantPermissionsTable::class);
        /** @var \App\Model\Entity\Grant\GrantPermission|null $permission */
        $permission = $table->find()
            ->select(['code'])
            ->where([
                'account_type' => SEn\AccountType::ADMIN->value,
                'id' => $permissionId,
                'is_active' => 1,
            ])
            ->first();

        if ($permission === null) {
            return false;
        }
        $permissionCode = Cast::toStringOrNull($permission->code);
        if ($permissionCode === null || $permissionCode === '') {
            return false;
        }

        return (new AdminAccountGrantRepository(new DateTimeImmutable()))
            ->detail(new AdminAccountId($accountId))
            ->hasPermissionCode(new Code($permissionCode));
    }

    /**
     * @param string $permissionCode
     * @return bool
     */
    public function hasPermissionCode(string $permissionCode): bool
    {
        /** @var \App\Model\Table\Grant\GrantPermissionsTable $table */
        $table = $this->fetchTable(GrantPermissionsTable::class);
        /** @var \App\Model\Entity\Grant\GrantPermission|null $permission */
        $permission = $table->find()
            ->select(['id'])
            ->where([
                'account_type' => SEn\AccountType::ADMIN->value,
                'code' => $permissionCode,
                'is_active' => 1,
            ])
            ->first();

        if ($permission === null) {
            return false;
        }

        return $this->hasPermission((int)$permission->id);
    }
}
