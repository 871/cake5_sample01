<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Cake\Log\PageAccessLogs;

use App\Domain\Log\PageAccessLogs\Entity\PageAccessLog as DomainEntity;
use App\Domain\Log\PageAccessLogs\Repository\PageAccessLogsRepository as DomainRepository;
use App\Domain\Log\PageAccessLogs\SearchCondition;

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
