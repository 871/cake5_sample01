<?php
declare(strict_types=1);

namespace App\Service\Controller\Admin;

use App\Infrastructure\Persistence\Cake\Admin\AdminGrantMapper;
use App\Model\Entity\Grant\GrantPermission;
use App\Model\Entity\Grant\GrantRole;
use App\Model\Entity\Shared\AccountStatusMaster;
use App\Model\Table\Grant\GrantPermissionsTable;
use App\Model\Table\Grant\GrantRolesTable;
use App\Model\Table\Shared\AccountStatusMastersTable;
use App\Service\Controller\Shared\ServiceInterface;
use App\Service\Controller\Shared\ServiceTrait;
use Cake\ORM\Locator\LocatorAwareTrait;

final class AdminGrant implements ServiceInterface
{
    use ServiceTrait;
    use LocatorAwareTrait;

    /**
     * @return array<int, array<string, string>>
     */
    public function getGrantRoleOptions(): array
    {
        /** @var \App\Model\Table\Grant\GrantRolesTable $table */
        $table = $this->fetchTable(GrantRolesTable::class);

        return $table->find()
            ->select(['id', 'name'])
            ->where([
                'account_type' => 'ADMIN',
                'is_active' => 1,
            ])
            ->orderBy(['sort' => 'ASC', 'id' => 'ASC'])
            ->all()
            ->map(fn(GrantRole $e): array => [
                'value' => (string)$e->id,
                'label' => (string)$e->name,
            ])
            ->toList();
    }

    /**
     * @return array<int, array<string, string>>
     */
    public function getGrantPermissionOptions(): array
    {
        /** @var \App\Model\Table\Grant\GrantPermissionsTable $table */
        $table = $this->fetchTable(GrantPermissionsTable::class);

        return $table->find()
            ->select(['id', 'name'])
            ->where([
                'account_type' => 'ADMIN',
                'is_active' => 1,
            ])
            ->orderBy(['sort' => 'ASC', 'id' => 'ASC'])
            ->all()
            ->map(fn(GrantPermission $e): array => [
                'value' => (string)$e->id,
                'label' => (string)$e->name,
            ])
            ->toList();
    }

    /**
     * @return array<int, array<string, string>>
     */
    public function getAccountStatusOptions(): array
    {
        /** @var \App\Model\Table\Shared\AccountStatusMastersTable $table */
        $table = $this->fetchTable(AccountStatusMastersTable::class);

        return $table->find()
            ->select(['id', 'name'])
            ->where(['is_active' => 1])
            ->orderBy(['sort' => 'ASC'])
            ->all()
            ->map(fn(AccountStatusMaster $e): array => [
                'value' => (string)$e->id,
                'label' => (string)$e->name,
            ])
            ->toList();
    }
}
