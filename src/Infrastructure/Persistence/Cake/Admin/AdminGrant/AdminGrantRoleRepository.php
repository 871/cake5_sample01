<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Cake\Admin\AdminGrant;

use App\Domain\Admin\AdminGrant\Entity\GrantRole as DomainEntity;
use App\Domain\Admin\AdminGrant\Repository\AdminGrantRoleRepository as DomainAdminGrantRoleRepository;
use App\Domain\Admin\AdminGrant\SearchAdminGrantRoleCondition;
use App\Domain\Admin\AdminGrant\ValueObject as Vo;
use Cake\ORM\Query\SelectQuery;
use DateTimeInterface;

final class AdminGrantRoleRepository implements DomainAdminGrantRoleRepository
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
     * ロール権限の検索
     *
     * Memo: Cake5のController::paginate()の仕様を優先した設計とするため、Cake\ORM\Queryを直接返す形にしています。 --- IGNORE ---
     *
     * @param \App\Domain\Admin\AdminGrant\SearchAdminGrantRoleCondition $condition
     * @return \Cake\ORM\Query\SelectQuery<\App\Model\Entity\Grant\GrantRole>
     */
    public function query(SearchAdminGrantRoleCondition $condition): SelectQuery
    {
        return (new AdminGrantRoleRepository\Query())->run($condition);
    }

    /**
     * ロール権限の検索
     *
     * @param \App\Domain\Admin\AdminGrant\SearchAdminGrantRoleCondition $condition
     * @return array<\App\Domain\Admin\AdminGrant\Entity\GrantRole>
     */
    public function search(SearchAdminGrantRoleCondition $condition): array
    {
        return (new AdminGrantRoleRepository\Search($this->datetime))->run($condition);
    }

    /**
     * ロール権限の作成
     *
     * @param \App\Domain\Admin\AdminGrant\Entity\GrantRole $entity
     * @return \App\Domain\Admin\AdminGrant\Entity\GrantRole
     */
    public function create(DomainEntity $entity): DomainEntity
    {
        return (new AdminGrantRoleRepository\Create($this->datetime))->run($entity);
    }

    /**
     * ロール権限の取得
     *
     * @param \App\Domain\Admin\AdminGrant\ValueObject\GrantRoleId $id
     * @return \App\Domain\Admin\AdminGrant\Entity\GrantRole
     */
    public function read(Vo\GrantRoleId $id): DomainEntity
    {
        return (new AdminGrantRoleRepository\Detail($this->datetime))->run($id);
    }

    /**
     * ロール権限の更新
     *
     * @param \App\Domain\Admin\AdminGrant\Entity\GrantRole $entity
     * @return \App\Domain\Admin\AdminGrant\Entity\GrantRole
     */
    public function update(DomainEntity $entity): DomainEntity
    {
        return (new AdminGrantRoleRepository\Update($this->datetime))->run($entity);
    }

    /**
     * ロール権限の削除
     *
     * @param \App\Domain\Admin\AdminGrant\Entity\GrantRole $entity
     * @return \App\Domain\Admin\AdminGrant\Entity\GrantRole
     */
    public function delete(DomainEntity $entity): DomainEntity
    {
        return (new AdminGrantRoleRepository\Delete($this->datetime))->run($entity);
    }
}
