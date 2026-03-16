<?php
declare(strict_types=1);

namespace App\Service\Controller\AdminAccounts\Accounts;

use App\Domain\Admin\AdminAccounts\Entity\AdminAccount as DomainEntity;
use App\Domain\Admin\AdminAccounts\ValueObject as Vo;
use App\Infrastructure\Persistence\Cake\Admin\AdminAccountsRepository;
use App\Model\Table\Admin\AdminAccountHistoriesTable;
use App\Security\Input\Cast;
use App\Security\Input\StrictCast;
use App\Service\Controller\Shared\ServiceInterface;
use App\Service\Controller\Shared\ServiceTrait;
use Cake\ORM\Locator\LocatorAwareTrait;

final class Detail implements ServiceInterface
{
    use ServiceTrait;
    use LocatorAwareTrait;

    /**
     * @return \App\Domain\Admin\AdminAccounts\Entity\AdminAccount
     */
    public function getDomainEntity(): DomainEntity
    {
        return (new AdminAccountsRepository())->read(
            new Vo\Id(
                StrictCast::toString($this->request->getParam('admin_account_id')),
            ),
        );
    }

    /**
     * @param string $adminAccountId
     * @return array<int, array<string, mixed>>
     */
    public function getHistories(string $adminAccountId): array
    {
        /** @var \App\Model\Table\Admin\AdminAccountHistoriesTable $historyTable */
        $historyTable = $this->fetchTable(AdminAccountHistoriesTable::class);

        return $historyTable
            ->find()
            ->select([
                'AdminAccountHistories__id' => 'AdminAccountHistories.id',
                'AdminAccountHistories__login_id' => 'AdminAccountHistories.login_id',
                'AdminAccountHistories__name' => 'AdminAccountHistories.name',
                'AdminAccountHistories__email' => 'AdminAccountHistories.email',
                'AdminAccountHistories__role_id' => 'AdminAccountHistories.role_id',
                'AdminAccountHistories__status' => 'AdminAccountHistories.status',
                'AdminAccountHistories__operated_at' => 'AdminAccountHistories.operated_at',
            ])
            ->where([
                'AdminAccountHistories.admin_account_id' => $adminAccountId,
            ])
            ->order([
                'AdminAccountHistories.operated_at' => 'DESC',
            ])
            ->formatResults(function ($results) {
                return $results->map(function ($entity) {
                    return [
                        'login_id' => Cast::toString($entity->login_id),
                        'name' => Cast::toString($entity->name),
                        'email' => Cast::toString($entity->email),
                        'role_id' => Cast::toString($entity->role_id),
                        'status' => Cast::toString($entity->status),
                        'operated_at' => $entity->operated_at?->format('Y-m-d H:i:s') ?? '',
                    ];
                });
            })
            ->toArray();
    }
}
