<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Cake\Admin;

use App\Domain\Admin\AdminAccounts\Entity\AdminAccount as DomainEntity;
use App\Domain\Admin\AdminAccounts\Repository\AdminAccountsRepository as DomainAdminAccountsRepository;
use App\Domain\Admin\AdminAccounts\SearchCondition;
use App\Domain\Admin\AdminAccounts\ValueObject as Vo;
use Cake\ORM\Query;

final class AdminAccountsRepository implements DomainAdminAccountsRepository
{
    /**
     * 検索
     *
     * @param \App\Domain\Admin\AdminAccounts\SearchCondition $condition
     * @return \Cake\ORM\Query
     */
    public function search(SearchCondition $condition): Query
    {
        return (new AdminAccountsRepository\Search($condition))->run();
    }

    /**
     * 作成
     *
     * @param \App\Domain\Admin\AdminAccounts\Entity\AdminAccount $domainEntity
     * @return \App\Domain\Admin\AdminAccounts\Entity\AdminAccount
     */
    public function create(DomainEntity $domainEntity): DomainEntity
    {
        return (new AdminAccountsRepository\Create($domainEntity))->run();
    }

    /**
     * 取得
     *
     * @param \App\Domain\Admin\AdminAccounts\ValueObject\Id $id
     * @return \App\Domain\Admin\AdminAccounts\Entity\AdminAccount
     */
    public function read(Vo\Id $id): DomainEntity
    {
        return (new AdminAccountsRepository\Read($id))->run();
    }

    /**
     * 更新
     *
     * @param \App\Domain\Admin\AdminAccounts\Entity\AdminAccount $domainEntity
     * @param bool $changePassword
     * @return \App\Domain\Admin\AdminAccounts\Entity\AdminAccount
     */
    public function update(DomainEntity $domainEntity, bool $changePassword = false): DomainEntity
    {
        return (new AdminAccountsRepository\Update($domainEntity, $changePassword))->run();
    }

    /**
     * 削除
     *
     * @param \App\Domain\Admin\AdminAccounts\ValueObject\Id $id
     * @return \App\Domain\Admin\AdminAccounts\Entity\AdminAccount
     */
    public function delete(Vo\Id $id): DomainEntity
    {
        return (new AdminAccountsRepository\Delete($id))->run();
    }
}
