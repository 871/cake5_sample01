<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Cake\Log\LoginLogs;

use App\Domain\Log\LoginLogs\Entity\LoginLog as DomainEntity;
use App\Domain\Log\LoginLogs\Repository\LoginLogsRepository as DomainRepository;
use App\Domain\Log\LoginLogs\SearchCondition;
use App\Domain\Log\LoginLogs\ValueObject as Vo;
use Cake\ORM\Query\SelectQuery;
use DateTimeInterface;

class LoginLogsRepository implements DomainRepository
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
     * Memo: Cake5のController::paginate()の仕様を優先した設計とするため、Cake\ORM\Queryを直接返す形にしています。 --- IGNORE ---
     * 完全なDDDへ再設計する場合は、ドメインサービス内でページネーションやソートの処理も完結させる形にすることも検討してください。 --- IGNORE ---
     *
     * @param \App\Domain\Log\LoginLogs\SearchCondition $condition
     * @return \Cake\ORM\Query\SelectQuery<\App\Model\Entity\Log\LoginLog>
     */
    public function search(SearchCondition $condition): SelectQuery
    {
        return (new LoginLogsRepository\Search($condition))->run();
    }

    /**
     * 作成
     *
     * @param \App\Domain\Log\LoginLogs\Entity\LoginLog $entity
     * @return \App\Domain\Log\LoginLogs\Entity\LoginLog
     */
    public function create(DomainEntity $entity): DomainEntity
    {
        return (new LoginLogsRepository\Create($entity))->run();
    }

    /**
     * 取得
     *
     * @param \App\Domain\Log\LoginLogs\ValueObject\Id $id
     * @return \App\Domain\Log\LoginLogs\Entity\LoginLog
     */
    public function read(Vo\Id $id): DomainEntity
    {
        return (new LoginLogsRepository\Read($id))->run();
    }
}
