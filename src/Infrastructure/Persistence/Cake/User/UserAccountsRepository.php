<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Cake\User;

use App\Domain\User\UserAccounts\Entity\UserAccount as DomainEntity;
use App\Domain\User\UserAccounts\Repository\UserAccountsRepository as DomainUserAccountsRepository;
use App\Domain\User\UserAccounts\SearchCondition;
use App\Domain\User\UserAccounts\ValueObject as Vo;
use Cake\ORM\Query\SelectQuery;
use DateTimeImmutable;
use DateTimeInterface;

final class UserAccountsRepository implements DomainUserAccountsRepository
{
    /**
     * @param \DateTimeInterface $datetime
     */
    public function __construct(
        private readonly DateTimeInterface $datetime = new DateTimeImmutable(),
    ) {
        // do nothing
    }

    /**
     * 検索
     *
     * @param \App\Domain\User\UserAccounts\SearchCondition $condition
     * @return \Cake\ORM\Query\SelectQuery<\App\Model\Entity\User\UserAccount>
     */
    public function search(SearchCondition $condition): array
    {
        return (new UserAccountsRepository\Search($condition))->run();
    }

    /**
     * 作成
     *
     * @param \App\Domain\User\UserAccounts\Entity\UserAccount $domainEntity
     * @return \App\Domain\User\UserAccounts\Entity\UserAccount
     */
    public function create(DomainEntity $domainEntity): DomainEntity
    {
        return (new UserAccountsRepository\Create($domainEntity))->run();
    }

    /**
     * 取得
     *
     * @param \App\Domain\User\UserAccounts\ValueObject\Id $id
     * @return \App\Domain\User\UserAccounts\Entity\UserAccount
     */
    public function read(Vo\Id $id): DomainEntity
    {
        return (new UserAccountsRepository\Read($id))->run();
    }

    /**
     * 更新
     *
     * @param \App\Domain\User\UserAccounts\Entity\UserAccount $domainEntity
     * @return \App\Domain\User\UserAccounts\Entity\UserAccount
     */
    public function update(DomainEntity $domainEntity): DomainEntity
    {
        return (new UserAccountsRepository\Update($domainEntity))->run();
    }

    /**
     * 削除
     *
     * @param \App\Domain\User\UserAccounts\ValueObject\Id $id
     * @return \App\Domain\User\UserAccounts\Entity\UserAccount
     */
    public function delete(Vo\Id $id): DomainEntity
    {
        return (new UserAccountsRepository\Delete($id, $this->datetime))->run();
    }

    /**
     * 履歴取得
     *
     * @param \App\Domain\User\UserAccounts\ValueObject\Id $userAccountId
     * @return array<\App\Domain\User\UserAccounts\Entity\UserAccountHistory>
     */
    public function readHistories(Vo\Id $userAccountId): array
    {
        return (new UserAccountsRepository\ReadHistories($userAccountId))->run();
    }

    /**
     * @param \App\Domain\User\UserAccounts\ValueObject\Email $email
     * @return ?\App\Domain\User\UserAccounts\Entity\UserAccount
     */
    public function findByEmail(Vo\Email $email): ?DomainEntity
    {
        return (new UserAccountsRepository\FindByEmail($email))->run();
    }
}
