<?php
declare(strict_types=1);

namespace App\Application\Controller\Admin\AdminGrant\Role;

use App\Application\Controller\Admin\AdminGrant as CategoryService;
use App\Application\Controller\Shared\ServiceInterface;
use App\Application\Controller\Shared\ServiceTrait;
use App\Domain\Admin\AdminGrant\SearchAdminGrantRoleCondition;
use App\Domain\Admin\AdminGrant\ValueObject\GrantPermissionId;
use App\Domain\Admin\AdminGrant\ValueObject\IsActive;
use App\Domain\Shared\ValueObject\SearchText;
use App\Infrastructure\Persistence\Cake\Admin\AdminGrant\AdminGrantRoleRepository;
use App\Security\Input\Cast;
use Cake\ORM\Query\SelectQuery;

final class Search implements ServiceInterface
{
    use ServiceTrait;

    /**
     * @return array<string, array<int, string>>
     */
    public function getInitParams(): array
    {
        return [
            'is_active' => ['1'],
        ];
    }

    /**
     * @return \Cake\ORM\Query\SelectQuery<\App\Model\Entity\Grant\GrantRole>
     */
    public function getSearchQuery(): array
    {
        return (new AdminGrantRoleRepository($this->datetime))->query(new SearchAdminGrantRoleCondition(
            searchText: new SearchText(Cast::toStringOrNull($this->request->getQuery('keyword'))),
            isActives: $this->getSearchIsActives(),
            grantPermissionIds: $this->getSearchGrantPermissionIds(),
        ));
    }

    /**
     * @return array<string, array<int|string, string>|int>
     */
    public function getPaginateSettings(): array
    {
        return [
            'limit' => 20,
            'maxLimit' => 200,
            'sortableFields' => [
                'GrantRoles.id',
                'GrantRoles.code',
                'GrantRoles.name',
                'GrantRoles.sort',
                'GrantRoles.modified',
            ],
            'order' => [
                'GrantRoles.sort' => 'ASC',
                'GrantRoles.id' => 'ASC',
            ],
        ];
    }

    /**
     * @return array<\App\Domain\Admin\AdminGrant\Entity\GrantPermission>
     */
    public function getGrantPermissionOptions(): array
    {
        /** @var \App\Application\Controller\Admin\AdminGrant $categoryService */
        $categoryService = $this->createService(CategoryService::class);

        return $categoryService->getAllGrantPermissionOptions();
    }

    /**
     * @return array<\App\Domain\Admin\AdminGrant\ValueObject\IsActive>
     */
    private function getSearchIsActives(): array
    {
        $values = array_values(array_unique(array_filter(array_map(
            fn($value) => Cast::toStringOrNull($value),
            (array)$this->request->getQuery('is_active', ['1']),
        ), fn($value) => in_array($value, ['0', '1'], true))));

        if ($values === []) {
            $values = ['1'];
        }

        return array_map(
            fn($value) => new IsActive($value),
            $values,
        );
    }

    /**
     * @return array<\App\Domain\Admin\AdminGrant\ValueObject\GrantPermissionId>
     */
    private function getSearchGrantPermissionIds(): array
    {
        return array_map(
            fn($value) => new GrantPermissionId($value),
            array_values(array_unique(array_filter(array_map(
                fn($value) => Cast::toStringOrNull($value),
                (array)$this->request->getQuery('grant_permission_id', []),
            )))),
        );
    }
}
