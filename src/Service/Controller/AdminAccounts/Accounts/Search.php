<?php
declare(strict_types=1);

namespace App\Service\Controller\AdminAccounts\Accounts;

use App\Domain\Admin\AdminAccounts\SearchCondition;
use App\Domain\Admin\AdminAccounts\ValueObject;
use App\Infrastructure\Persistence\Cake\Admin\AdminAccountsRepository;
use App\Infrastructure\Persistence\Cake\Admin\AdminRolesRepository;
use App\Service\Controller\Shared\ServiceInterface;
use App\Service\Controller\Shared\ServiceTrait;
use Cake\ORM\Query;

final class Search implements ServiceInterface
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
     * @return \Cake\ORM\Query
     */
    public function getSearchQuery(): Query
    {
        /** @var array<string, string> $data */
        $data = $this->request->getQuery();

        return (new AdminAccountsRepository())->search(new SearchCondition(
            keyword: ValueObject\Search\Keyword::fromString($data['keyword'] ?? null),
            roleId: ValueObject\RoleId::fromString($data['role_id'] ?? null),
            status: ValueObject\Status::fromString($data['status'] ?? null),
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
                'id',
                'login_id',
                'name',
                'email',
                'role_id',
                'status',
            ],
            'order' => [
                'id' => 'DESC',
            ],
        ];
    }

    /**
     * @return \App\Domain\Admin\AdminRoles\Entity\AdminRole[]
     */
    public function getRoles(): array
    {
        return (new AdminRolesRepository())->findAll();
    }
}
