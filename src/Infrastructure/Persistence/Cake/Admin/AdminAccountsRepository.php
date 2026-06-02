<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Cake\Admin;

use App\Domain\Admin\AdminAccounts\Entity\AdminAccount as DomainEntity;
use App\Domain\Admin\AdminAccounts\Repository\AdminAccountsRepository as DomainAdminAccountsRepository;
use App\Domain\Admin\AdminAccounts\SearchCondition;
use App\Domain\Admin\AdminAccounts\ValueObject as Vo;
use Cake\ORM\Query\SelectQuery;
use DateTimeInterface;

final class AdminAccountsRepository implements DomainAdminAccountsRepository
{
    /**
     * @param \DateTimeInterface $datetime
     */
    public function __construct(
        private readonly DateTimeInterface $datetime,
    ) {
        // do nothing
    }

    /**
     * 検索
     *
     * @param \App\Domain\Admin\AdminAccounts\SearchCondition $condition
     * @return \Cake\ORM\Query\SelectQuery<\App\Model\Entity\Admin\AdminAccount>
     */
    public function search(SearchCondition $condition): array
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
     * @return \App\Domain\Admin\AdminAccounts\Entity\AdminAccount
     */
    public function update(DomainEntity $domainEntity): DomainEntity
    {
        return (new AdminAccountsRepository\Update($domainEntity))->run();
    }

    /**
     * 削除
     *
     * @param \App\Domain\Admin\AdminAccounts\ValueObject\Id $id
     * @return \App\Domain\Admin\AdminAccounts\Entity\AdminAccount
     */
    public function delete(Vo\Id $id): DomainEntity
    {
        return (new AdminAccountsRepository\Delete($id, $this->datetime))->run();
    }

    /**
     * 履歴取得
     *
     * @param \App\Domain\Admin\AdminAccounts\ValueObject\Id $adminAccountId
     * @return array<\App\Domain\Admin\AdminAccounts\Entity\AdminAccountHistory>
     */
    public function readHistories(Vo\Id $adminAccountId): array
    {
        return (new AdminAccountsRepository\ReadHistories($adminAccountId))->run();
    }

    /**
     * @param \App\Domain\Admin\AdminAccounts\ValueObject\Email $email
     * @return ?\App\Domain\Admin\AdminAccounts\Entity\AdminAccount
     */
    public function findByEmail(Vo\Email $email): ?DomainEntity
    {
        return (new AdminAccountsRepository\FindByEmail($email))->run();
    }
}
