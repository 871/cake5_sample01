<?php
declare(strict_types=1);

namespace App\Service\Controller\Other\AdminAccount;

use App\Domain\Admin\AdminAccounts\Entity\AdminAccount as DomainEntity;
use App\Domain\Admin\AdminAccounts\ValueObject as Vo;
use App\Infrastructure\Persistence\Cake\Admin\AdminAccountsRepository;
use App\Security\Input\StrictCast;
use App\Service\Controller\Shared\ServiceInterface;
use App\Service\Controller\Shared\ServiceTrait;

final class Detail implements ServiceInterface
{
    use ServiceTrait;

    /**
     * @return \App\Domain\Admin\AdminAccounts\Entity\AdminAccount
     */
    public function getDomainEntity(): DomainEntity
    {
        return (new AdminAccountsRepository($this->datetime))->read(
            new Vo\Id(
                StrictCast::toString($this->request->getParam('admin_account_id')),
            ),
        );
    }

    /**
     * @return array<\App\Domain\Admin\AdminAccounts\Entity\AdminAccountHistory>
     */
    public function getHistories(): array
    {
        return (new AdminAccountsRepository($this->datetime))->readHistories(
            new Vo\Id(
                StrictCast::toString($this->request->getParam('admin_account_id')),
            ),
        );
    }
}
