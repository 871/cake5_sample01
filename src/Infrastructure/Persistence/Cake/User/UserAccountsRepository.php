<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Cake\User;

use App\Model\Entity\User\UserAccount;
use App\Model\Table\User\UserAccountsTable;
use Cake\ORM\Locator\LocatorAwareTrait;

class UserAccountsRepository
{
    use LocatorAwareTrait;

    /**
     * @var \App\Model\Table\User\UserAccountsTable
     */
    private UserAccountsTable $table;

    public function __construct()
    {
        $this->table = $this->fetchTable(UserAccountsTable::class);
    }

    /**
     * @param string $email
     * @return ?\App\Model\Entity\User\UserAccount
     */
    public function findByEmail(string $email): ?UserAccount
    {
        /** @var \App\Model\Entity\User\UserAccount|null $account */
        $account = $this->table
            ->find()
            ->contain(['AccountStatusMasters'])
            ->where([
                'UserAccounts.email' => $email,
            ])
            ->first();

        return $account;
    }

    /**
     * @param string $id
     * @return ?\App\Model\Entity\User\UserAccount
     */
    public function read(string $id): ?UserAccount
    {
        if (!ctype_digit($id)) {
            return null;
        }

        /** @var \App\Model\Entity\User\UserAccount|null $account */
        $account = $this->table
            ->find()
            ->contain(['AccountStatusMasters'])
            ->where([
                'UserAccounts.id' => (int)$id,
            ])
            ->first();

        return $account;
    }
}
