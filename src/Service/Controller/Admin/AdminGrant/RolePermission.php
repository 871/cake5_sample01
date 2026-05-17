<?php
declare(strict_types=1);

namespace App\Service\Controller\Admin\AdminGrant;

use App\Domain\Admin\AdminGrant\Entity\GrantRolePermission;
use App\Domain\Admin\AdminGrant\SearchRolePermissionCondition;
use App\Domain\Admin\AdminGrant\ValueObject\GrantPermissionId;
use App\Domain\Admin\AdminGrant\ValueObject\GrantRoleId;
use App\Domain\Admin\AdminGrant\ValueObject\GrantRolePermissionId;
use App\Domain\Shared\ValueObject\SearchText;
use App\Infrastructure\Persistence\Cake\Admin\AdminGrantRepository;
use App\Service\Controller\Admin\AdminGrant as CategoryService;
use App\Service\Controller\Shared\ServiceInterface;
use App\Service\Controller\Shared\ServiceTrait;
use Cake\ORM\Query\SelectQuery;

final class RolePermission implements ServiceInterface
{
    use ServiceTrait;

    /**
     * @return array<string, string>
     */
    public function getInitParams(): array
    {
        return [];
    }

    /**
     * @return \Cake\ORM\Query\SelectQuery<\App\Model\Entity\Grant\GrantRolePermission>
     */
    public function getSearchQuery(): SelectQuery
    {
        return (new AdminGrantRepository($this->datetime))->searchRolePermission(
            new SearchRolePermissionCondition(
                searchText: SearchText::fromString((string)$this->request->getQuery('search_text')),
            ),
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function getPaginateSettings(): array
    {
        return [
            'limit' => 20,
            'maxLimit' => 200,
            'sortableFields' => [
                'GrantRolePermissions.grant_role_id',
                'GrantRolePermissions.grant_permission_id',
            ],
            'order' => [
                'GrantRolePermissions.grant_role_id' => 'ASC',
                'GrantRolePermissions.grant_permission_id' => 'ASC',
            ],
        ];
    }

    /**
     * @return array<int, array<string, string>>
     */
    public function getGrantRoleOptions(): array
    {
        /** @var \App\Service\Controller\Admin\AdminGrant $category */
        $category = $this->createService(CategoryService::class);

        return $category->getGrantRoleOptions();
    }

    /**
     * @return array<int, array<string, string>>
     */
    public function getGrantPermissionOptions(): array
    {
        /** @var \App\Service\Controller\Admin\AdminGrant $category */
        $category = $this->createService(CategoryService::class);

        return $category->getGrantPermissionOptions();
    }

    /**
     * @return void
     */
    public function createFromRequest(): void
    {
        $data = (array)$this->request->getData();

        (new AdminGrantRepository($this->datetime))->createRolePermission(new GrantRolePermission(
            id: null,
            grant_role_id: (string)($data['grant_role_id'] ?? ''),
            grant_permission_id: (string)($data['grant_permission_id'] ?? ''),
            created: $this->datetime->format('Y-m-d\TH:i:s'),
            modified: $this->datetime->format('Y-m-d\TH:i:s'),
        ));
    }

    /**
     * @param string $id
     * @return \App\Domain\Admin\AdminGrant\Entity\GrantRolePermission
     */
    public function read(string $id): GrantRolePermission
    {
        return (new AdminGrantRepository($this->datetime))->readRolePermission(new GrantRolePermissionId($id));
    }

    /**
     * @param string $id
     * @return void
     */
    public function delete(string $id): void
    {
        (new AdminGrantRepository($this->datetime))->deleteRolePermission(new GrantRolePermissionId($id));
    }

    /**
     * @param string $grantRoleId
     * @return void
     */
    public function update(string $grantRoleId): void
    {
        $grantPermissionIds = array_map(
            fn(string $value): GrantPermissionId => new GrantPermissionId($value),
            array_values(array_filter(array_map(
                fn($value): string => (string)$value,
                (array)$this->request->getData('grant_permission_ids'),
            ), fn(string $value): bool => $value !== '')),
        );

        (new AdminGrantRepository($this->datetime))->updateRolePermissions(
            new GrantRoleId($grantRoleId),
            $grantPermissionIds,
        );
    }
}
