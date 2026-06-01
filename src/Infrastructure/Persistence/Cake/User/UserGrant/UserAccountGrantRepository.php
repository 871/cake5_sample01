<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Cake\User\UserGrant;

use App\Domain\User\UserGrant\Entity\UserAccountGrant;
use App\Domain\User\UserGrant\Repository\UserAccountGrantRepository as InterfaceRepo;
use App\Domain\User\UserGrant\SearchUserAccountGrantCondition;
use App\Domain\User\UserGrant\ValueObject as Vo;
use Cake\ORM\Query\SelectQuery;
use DateTimeInterface;

final class UserAccountGrantRepository implements InterfaceRepo
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
     * ユーザ権限の検索
     *
     * アカウントと権限の組み合わせでユニークなレコードを検索する
     *
     * @param \App\Domain\User\UserGrant\SearchUserAccountGrantCondition $condition
     * @return \Cake\ORM\Query\SelectQuery<\App\Model\Entity\User\UserAccount>
     */
    public function search(SearchUserAccountGrantCondition $condition): SelectQuery
    {
        return (new UserAccountGrantRepository\Search())->run($condition);
    }

    /**
     * ユーザステータスマスタの取得
     *
     * @return array<\App\Domain\User\UserGrant\Entity\AccountStatusMaster>
     */
    public function findAccountStatusMasters(): array
    {
        return (new UserAccountGrantRepository\FindAccountStatusMasters())->run();
    }

    /**
     * ユーザ権限情報の取得
     *
     * 指定したユーザアカウントが保持している権限一覧を取得する（ロール経由・個別付与を含む）
     *
     * @param \App\Domain\User\UserGrant\ValueObject\UserAccountId $userAccountId
     * @return \App\Domain\User\UserGrant\Entity\UserAccountGrant
     */
    public function detail(Vo\UserAccountId $userAccountId): UserAccountGrant
    {
        return (new UserAccountGrantRepository\Detail())->run($userAccountId);
    }

    /**
     * ユーザ権限の設定（ロール、個別を同時）
     *
     * 指定したユーザアカウントのロール付与と個別権限付与を一括で設定する（既存設定は削除して再設定）
     *
     * @param \App\Domain\User\UserGrant\Entity\UserAccountGrant $userAccountGrant
     * @return \App\Domain\User\UserGrant\Entity\UserAccountGrant
     */
    public function save(UserAccountGrant $userAccountGrant): UserAccountGrant
    {
        return (new UserAccountGrantRepository\Save($this->datetime))->run($userAccountGrant);
    }
}
