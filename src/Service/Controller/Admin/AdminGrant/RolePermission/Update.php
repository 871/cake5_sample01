<?php
declare(strict_types=1);

namespace App\Service\Controller\Admin\AdminGrant\RolePermission;

use App\Domain\Admin\AdminGrant\ValueObject\GrantPermissionId;
use App\Domain\Admin\AdminGrant\ValueObject\GrantRoleId;
use App\Infrastructure\Persistence\Cake\Admin\AdminGrantRepository;
use App\Service\Controller\Admin\AdminGrant as CategoryService;
use App\Service\Controller\Shared\ServiceInterface;
use App\Service\Controller\Shared\ServiceTrait;

final class Update implements ServiceInterface
{
    use ServiceTrait;

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
     * @param string $grantRoleId
     * @return array<int, string>
     */
    public function getSelectedPermissionIds(string $grantRoleId): array
    {
        return collection($this->getSearchQuery()->all())
            ->filter(fn($row) => (string)$row->grant_role_id === $grantRoleId)
            ->map(fn($row) => (string)$row->grant_permission_id)
            ->toList();
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

    /**
     * @return \Cake\ORM\Query\SelectQuery<\App\Model\Entity\Grant\GrantRolePermission>
     */
    private function getSearchQuery(): \Cake\ORM\Query\SelectQuery
    {
        /** @var \App\Service\Controller\Admin\AdminGrant\RolePermission\Search $searchService */
        $searchService = $this->createService(Search::class);

        return $searchService->getSearchQuery();
    }
}
