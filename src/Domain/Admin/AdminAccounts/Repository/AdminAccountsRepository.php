<?php
declare(strict_types=1);

namespace App\Domain\Admin\AdminAccounts\Repository;

use App\Domain\Admin\AdminAccounts\Entity\AdminAccount;
use App\Domain\Admin\AdminAccounts\SearchCondition;
use App\Domain\Admin\AdminAccounts\ValueObject as Vo;
use Cake\ORM\Query\SelectQuery;
use DateTimeInterface;

interface AdminAccountsRepository
{
    /**
     * @param \DateTimeInterface $datetime
     */
    public function __construct(DateTimeInterface $datetime);

    /**
     * @param \App\Domain\Admin\AdminAccounts\SearchCondition $condition
     * @return \Cake\ORM\Query\SelectQuery<\App\Model\Entity\Admin\AdminAccount>
     */
    public function search(SearchCondition $condition): SelectQuery;

    /**
     * @param \App\Domain\Admin\AdminAccounts\Entity\AdminAccount $entity
     * @return \App\Domain\Admin\AdminAccounts\Entity\AdminAccount
     */
    public function create(AdminAccount $entity): AdminAccount;

    /**
     * @param \App\Domain\Admin\AdminAccounts\ValueObject\Id $id
     * @return \App\Domain\Admin\AdminAccounts\Entity\AdminAccount
     */
    public function read(Vo\Id $id): AdminAccount;

    /**
     * @param \App\Domain\Admin\AdminAccounts\Entity\AdminAccount $entity
     * @return \App\Domain\Admin\AdminAccounts\Entity\AdminAccount
     */
    public function update(AdminAccount $entity): AdminAccount;

    /**
     * @param \App\Domain\Admin\AdminAccounts\ValueObject\Id $id
     * @return \App\Domain\Admin\AdminAccounts\Entity\AdminAccount
     */
    public function delete(Vo\Id $id): AdminAccount;
}
