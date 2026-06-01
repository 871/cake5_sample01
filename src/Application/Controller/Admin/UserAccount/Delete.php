<?php
declare(strict_types=1);

namespace App\Application\Controller\Admin\UserAccount;

use App\Application\Controller\Shared\ServiceInterface;
use App\Application\Controller\Shared\ServiceTrait;
use App\Domain\User\UserAccounts\Entity\UserAccount;
use App\Domain\User\UserAccounts\ValueObject as Vo;
use App\Infrastructure\Persistence\Cake\User\UserAccountsRepository;
use App\Security\Input\StrictCast;

final class Delete implements ServiceInterface
{
    use ServiceTrait;

    /**
     * @return \App\Domain\User\UserAccounts\Entity\UserAccount
     */
    public function delete(): UserAccount
    {
        return (new UserAccountsRepository($this->datetime))->delete(
            new Vo\Id(
                StrictCast::toString($this->request->getParam('user_account_id')),
            ),
        );
    }
}
