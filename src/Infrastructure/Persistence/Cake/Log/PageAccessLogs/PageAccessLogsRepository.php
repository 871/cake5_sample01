<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Cake\Log\PageAccessLogs;

use App\Domain\Log\PageAccessLogs\AdminSearchCondition;
use App\Domain\Log\PageAccessLogs\Entity\PageAccessLog as DomainEntity;
use App\Domain\Log\PageAccessLogs\Repository\PageAccessLogsRepository as DomainRepository;
use App\Domain\Log\PageAccessLogs\SearchCondition;
use Cake\ORM\Query\SelectQuery;

class PageAccessLogsRepository implements DomainRepository
{
    /**
     * 検索（カーソルベースページネーション、COUNTなし）
     *
     * @param \App\Domain\Log\PageAccessLogs\SearchCondition $condition
     * @return array<\App\Domain\Log\PageAccessLogs\Entity\PageAccessLog>
     */
    public function search(SearchCondition $condition): array
    {
        return (new PageAccessLogsRepository\Search($condition))->run();
    }

    /**
     * Memo: Cake5のController::paginate()の仕様を優先した設計とするため、Cake\ORM\Queryを直接返す形にしています。 --- IGNORE ---
     * 完全なDDDへ再設計する場合は、ドメインサービス内でページネーションやソートの処理も完結させる形にすることも検討してください。 --- IGNORE ---
     *
     * @param \App\Domain\Log\PageAccessLogs\AdminSearchCondition $condition
     * @return \Cake\ORM\Query\SelectQuery<\App\Model\Entity\Log\PageAccessLog>
     */
    public function adminSearch(AdminSearchCondition $condition): SelectQuery
    {
        return (new PageAccessLogsRepository\AdminSearch($condition))->run();
    }

    /**
     * 作成
     *
     * @param \App\Domain\Log\PageAccessLogs\Entity\PageAccessLog $entity
     * @return \App\Domain\Log\PageAccessLogs\Entity\PageAccessLog
     */
    public function create(DomainEntity $entity): DomainEntity
    {
        return (new PageAccessLogsRepository\Create($entity))->run();
    }
}
