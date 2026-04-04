<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Cake\Log\PageAccessLogs\PageAccessLogsRepository;

use App\Domain\Log\PageAccessLogs\AdminSearchCondition;
use App\Model\Table\Log\PageAccessLogsTable;
use Cake\ORM\Locator\LocatorAwareTrait;
use Cake\ORM\Query\SelectQuery;

final class AdminSearch
{
    use LocatorAwareTrait;

    /**
     * @var \App\Model\Table\Log\PageAccessLogsTable
     */
    private PageAccessLogsTable $table;

    /**
     * @param \App\Domain\Log\PageAccessLogs\AdminSearchCondition $condition
     */
    public function __construct(
        private readonly AdminSearchCondition $condition,
    ) {
        $this->table = $this->fetchTable(PageAccessLogsTable::class);
    }

    /**
     * @return \Cake\ORM\Query\SelectQuery<\App\Model\Entity\Log\PageAccessLog>
     */
    public function run(): SelectQuery
    {
        return $this->table
            ->find()
            ->contain(['AdminAccounts'])
            ->where(
                array_filter([
                    'PageAccessLogs.accessed >='
                        => $this->condition->getAccessedFrom()->format('Y-m-d\TH:i:s'),
                    'PageAccessLogs.accessed <='
                        => $this->condition->getAccessedTo()->format('Y-m-d\TH:i:s'),
                    'PageAccessLogs.account_type'
                        => $this->condition->getAccountType()->toStringOrNull(),
                    'PageAccessLogs.account_id'
                        => $this->condition->getAccountId()->toStringOrNull(),
                    array_filter([
                        'OR' => array_map(function (string $likeWord) {
                            return [
                                'OR' => [
                                    'PageAccessLogs.path LIKE' => $likeWord,
                                    'PageAccessLogs.route_name LIKE' => $likeWord,
                                    'PageAccessLogs.ip_address LIKE' => $likeWord,
                                    'PageAccessLogs.user_agent LIKE' => $likeWord,
                                    'AdminAccounts.name LIKE' => $likeWord,
                                ],
                            ];
                        }, $this->condition->getKeyword()->toQueryLikeList()),
                    ], fn($v) => !in_array($v, [null, '', []], true)),
                ], fn($v) => !in_array($v, [null, '', []], true)),
            );
    }
}
