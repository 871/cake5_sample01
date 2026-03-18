<?php
declare(strict_types=1);

namespace App\Service\Controller\Other\AdminAccount;

use App\Domain\Admin\AdminAccounts\SearchCondition;
use App\Domain\Admin\AdminAccounts\ValueObject;
use App\Infrastructure\Persistence\Cake\Admin\AdminAccountsRepository;
use App\Service\Controller\Other\AdminAccount as CategoryService;
use App\Service\Controller\Shared\ServiceInterface;
use App\Service\Controller\Shared\ServiceTrait;
use Cake\ORM\Locator\LocatorAwareTrait;
use Cake\ORM\Query;

final class Search implements ServiceInterface
{
    use ServiceTrait;
    use LocatorAwareTrait;

    /**
     * @return array<string, mixed>
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

        return (new AdminAccountsRepository($this->datetime))->search(new SearchCondition(
            id: new ValueObject\Id($data['id'] ?? null),
            keyword: ValueObject\Search\Keyword::fromString($data['keyword'] ?? null),
            accountStatusMasterId: new ValueObject\AccountStatusMasterId($data['account_status_master_id'] ?? null),
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
                'email',
                'name',
                'account_status_master_id',
                'is_email_verified',
                'password_changed_at',
                'password_expires_at',
                'created',
                'modified',
            ],
            'order' => [
                'id' => 'DESC',
            ],
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getAccountStatusOptions(): array
    {
        /** @var \App\Service\Controller\Other\AdminAccount $categoryService */
        $categoryService = $this->createService(CategoryService::class);

        return $categoryService->getAccountStatusOptions();
    }
}
