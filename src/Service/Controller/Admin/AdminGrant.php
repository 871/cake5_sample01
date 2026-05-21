<?php
declare(strict_types=1);

namespace App\Service\Controller\Admin;

use App\Domain\Admin\AdminGrant\SearchAdminGrantPermissionCondition;
use App\Domain\Admin\AdminGrant\SearchAdminGrantRoleCondition;
use App\Domain\Admin\AdminGrant\ValueObject as Vo;
use App\Domain\Shared\ValueObject as SVo;
use App\Infrastructure\Persistence\Cake\Admin\AdminGrant\AdminAccountGrantRepository;
use App\Infrastructure\Persistence\Cake\Admin\AdminGrant\AdminGrantPermissionRepository;
use App\Infrastructure\Persistence\Cake\Admin\AdminGrant\AdminGrantRoleRepository;
use App\Model\Entity\Grant\GrantPermission;
use App\Model\Entity\Grant\GrantRole;
use App\Model\Entity\Shared\AccountStatusMaster;
use App\Model\Table\Grant\GrantPermissionsTable;
use App\Model\Table\Grant\GrantRolesTable;
use App\Model\Table\Shared\AccountStatusMastersTable;
use App\Security\Input\Cast;
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
        /*
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
            */

        return (new AdminGrantRoleRepository($this->datetime))->search(
            condition: new SearchAdminGrantRoleCondition(
                searchText: new SVo\SearchText(null),
                isActive: new Vo\IsActive('1'),
            ),
        );
    }

    /**
     * @return array<int, array<string, string>>
     */
    public function getGrantPermissionOptions(): array
    {
        return (new AdminGrantPermissionRepository($this->datetime))->search(
            condition: new SearchAdminGrantPermissionCondition(
                searchText: new SVo\SearchText(null),
                isActive: new Vo\IsActive('1'),
            ),
        );
    }

    /**
     * @return array<int, array<string, string>>
     */
    public function getAccountStatusOptions(): array
    {
        return (new AdminAccountGrantRepository($this->datetime))->findAccountStatusMasters();
    }
}
