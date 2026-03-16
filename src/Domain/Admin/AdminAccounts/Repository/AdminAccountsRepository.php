<?php
declare(strict_types=1);

namespace App\Domain\Admin\AdminAccounts\Repository;

use App\Domain\Admin\AdminAccounts\Entity\AdminAccount;
use App\Domain\Admin\AdminAccounts\SearchCondition;
use App\Domain\Admin\AdminAccounts\ValueObject as Vo;
use Cake\ORM\Query;

interface AdminAccountsRepository
{
    /**
     * 検索
     *
     * Memo: Cake5のController::paginate()の仕様を優先した設計とするため、Cake\ORM\Queryを直接返す形にしています。
     *
     * @param \App\Domain\Admin\AdminAccounts\SearchCondition $condition
     * @return \Cake\ORM\Query
     */
    public function search(SearchCondition $condition): Query;

    /**
     * 作成
     *
     * @param \App\Domain\Admin\AdminAccounts\Entity\AdminAccount $entity
     * @return \App\Domain\Admin\AdminAccounts\Entity\AdminAccount
     */
    public function create(AdminAccount $entity): AdminAccount;

    /**
     * 取得
     *
     * @param \App\Domain\Admin\AdminAccounts\ValueObject\Id $id
     * @return \App\Domain\Admin\AdminAccounts\Entity\AdminAccount
     */
    public function read(Vo\Id $id): AdminAccount;

    /**
     * 更新
     *
     * @param \App\Domain\Admin\AdminAccounts\Entity\AdminAccount $entity
     * @param bool $changePassword
     * @return \App\Domain\Admin\AdminAccounts\Entity\AdminAccount
     */
    public function update(AdminAccount $entity, bool $changePassword = false): AdminAccount;

    /**
     * 削除
     *
     * @param \App\Domain\Admin\AdminAccounts\ValueObject\Id $id
     * @return \App\Domain\Admin\AdminAccounts\Entity\AdminAccount
     */
    public function delete(Vo\Id $id): AdminAccount;
}
