<?php
declare(strict_types=1);

namespace App\Application\Controller\Admin\UserAccount;

use App\Application\Controller\Shared\ServiceInterface;
use App\Application\Controller\Shared\ServiceTrait;
use App\Domain\User\UserAccounts\Entity\UserAccount as DomainEntity;
use App\Domain\User\UserAccounts\ValueObject as Vo;
use App\Infrastructure\Persistence\Cake\User\UserAccountsRepository;
use App\Security\Input\StrictCast;

final class Detail implements ServiceInterface
{
    use ServiceTrait;

    /**
     * @return \App\Domain\User\UserAccounts\Entity\UserAccount
     */
    public function getDomainEntity(): DomainEntity
    {
        return (new UserAccountsRepository($this->datetime))->read(
            new Vo\Id(
                StrictCast::toString($this->request->getParam('user_account_id')),
            ),
        );
    }

    /**
     * @return array<\App\Domain\User\UserAccounts\Entity\UserAccountHistory>
     */
    public function getHistories(): array
    {
        return (new UserAccountsRepository($this->datetime))->readHistories(
            new Vo\Id(
                StrictCast::toString($this->request->getParam('user_account_id')),
            ),
        );
    }
}
