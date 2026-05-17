<?php
declare(strict_types=1);

namespace App\View\Helper;

use App\Domain\Admin\AdminGrant\ValueObject\AdminAccountId;
use App\Domain\Admin\AdminGrant\ValueObject\GrantPermissionId;
use App\Infrastructure\Persistence\Cake\Admin\AdminGrantMapper;
use App\Infrastructure\Persistence\Cake\Admin\AdminGrantRepository;
use App\Model\Table\Grant\GrantPermissionsTable;
use Cake\ORM\Locator\LocatorAwareTrait;
use Cake\View\Helper;
use DateTimeImmutable;

class AdminGrantHelper extends Helper
{
    use LocatorAwareTrait;

    /**
     * @param int|string $permissionId
     * @return bool
     */
    public function hasPermission(int|string $permissionId): bool
    {
        $accountId = (string)$this->getView()->getRequest()->getParam('account_id');
        if ($accountId === '') {
            return false;
        }

        return (new AdminGrantRepository(new DateTimeImmutable()))->hasPermission(
            new AdminAccountId($accountId),
            new GrantPermissionId((string)$permissionId),
        );
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
                'account_type' => AdminGrantMapper::ACCOUNT_TYPE,
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
