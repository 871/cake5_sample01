<?php
declare(strict_types=1);

namespace App\Service\Controller\AdminAccounts\Accounts;

use App\Domain\Admin\AdminAccounts\Entity\AdminAccount;
use App\Domain\Admin\AdminAccounts\ValueObject as Vo;
use App\Infrastructure\Persistence\Cake\Admin\AdminAccountsRepository;
use App\Security\Input\StrictCast;
use App\Service\Controller\Shared\ServiceInterface;
use App\Service\Controller\Shared\ServiceTrait;

final class Delete implements ServiceInterface
{
    use ServiceTrait;

    /**
     * @return \App\Domain\Admin\AdminAccounts\Entity\AdminAccount
     */
    public function delete(): AdminAccount
    {
        return (new AdminAccountsRepository())->delete(
            new Vo\Id(
                StrictCast::toString($this->request->getParam('admin_account_id')),
            ),
        );
    }
}
