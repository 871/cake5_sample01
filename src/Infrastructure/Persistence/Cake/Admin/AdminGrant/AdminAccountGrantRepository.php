<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Cake\Admin\AdminGrant;

use App\Domain\Admin\AdminGrant\Entity\AdminAccountGrant;
use App\Domain\Admin\AdminGrant\Repository\AdminAccountGrantRepository as DomainAdminAccountGrantRepository;
use App\Domain\Admin\AdminGrant\SearchAdminAccountGrantCondition;
use App\Domain\Admin\AdminGrant\ValueObject as Vo;
use Cake\ORM\Query\SelectQuery;
use DateTimeInterface;

final class AdminAccountGrantRepository implements DomainAdminAccountGrantRepository
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
     * 管理者権限の検索
     *
     * アカウントと権限の組み合わせでユニークなレコードを検索する
     *
     * Memo: Cake5のController::paginate()の仕様を優先した設計とするため、Cake\ORM\Queryを直接返す形にしています。 --- IGNORE ---
     *
     * @param \App\Domain\Admin\AdminGrant\SearchAdminAccountGrantCondition $condition
     * @return \Cake\ORM\Query\SelectQuery<\App\Model\Entity\Admin\AdminAccount>
     */
    public function search(SearchAdminAccountGrantCondition $condition): SelectQuery
    {
        return (new AdminAccountGrantRepository\Search($condition))->run();
    }

    /**
     * 管理者権限情報の取得
     *
     * 指定した管理者アカウントが保持している権限一覧を取得する（ロール経由・個別付与を含む）
     *
     * @param \App\Domain\Admin\AdminGrant\ValueObject\AdminAccountId $adminAccountId
     * @return \App\Domain\Admin\AdminGrant\Entity\AdminAccountGrant
     */
    public function detail(Vo\AdminAccountId $adminAccountId): AdminAccountGrant
    {
        return (new AdminAccountGrantRepository\Detail($adminAccountId))->run();
    }

    /**
     * 管理者権限の設定（ロール、個別を同時）
     *
     * 指定した管理者アカウントのロール付与と個別権限付与を一括で設定する（既存設定は削除して再設定）
     *
     * @param \App\Domain\Admin\AdminGrant\Entity\AdminAccountGrant $adminAccountGrant
     * @return \App\Domain\Admin\AdminGrant\Entity\AdminAccountGrant
     */
    public function save(AdminAccountGrant $adminAccountGrant): AdminAccountGrant
    {
        return (new AdminAccountGrantRepository\Save($adminAccountGrant))->run();
    }
}
