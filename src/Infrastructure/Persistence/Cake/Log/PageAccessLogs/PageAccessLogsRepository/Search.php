<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Cake\Log\PageAccessLogs\PageAccessLogsRepository;

use App\Domain\Log\PageAccessLogs\Entity\PageAccessLog as DomainEntity;
use App\Domain\Log\PageAccessLogs\SearchCondition;
use App\Domain\Log\PageAccessLogs\ValueObject\Search\NavigationType;
use App\Infrastructure\Persistence\Cake\Log\PageAccessLogs\PageAccessLogMapper;
use App\Model\Table\Log\PageAccessLogsTable;
use Cake\ORM\Locator\LocatorAwareTrait;

final class Search
{
    use LocatorAwareTrait;

    /**
     * @var \App\Model\Table\Log\PageAccessLogsTable
     */
    private PageAccessLogsTable $table;

    /**
     * @var \App\Infrastructure\Persistence\Cake\Log\PageAccessLogs\PageAccessLogMapper
     */
    private PageAccessLogMapper $mapper;

    /**
     * @param \App\Domain\Log\PageAccessLogs\SearchCondition $condition
     */
    public function __construct(
        private readonly SearchCondition $condition,
    ) {
        $this->table = $this->fetchTable(PageAccessLogsTable::class);
        $this->mapper = new PageAccessLogMapper();
    }

    /**
     * @return array<\App\Domain\Log\PageAccessLogs\Entity\PageAccessLog>
     */
    public function run(): array
    {
        return match ($this->condition->getNavigationType()->toString()) {
            NavigationType::FIRST => $this->searchFirst(),
            NavigationType::LAST => $this->searchLast(),
            NavigationType::NEXT => $this->searchNext(),
            NavigationType::PREV => $this->searchPrev(),
            default => throw new \LogicException('Unknown navigation type: ' . $this->condition->getNavigationType()->toString()),
        };
    }

    /**
     * @return array<\App\Domain\Log\PageAccessLogs\Entity\PageAccessLog>
     */
    private function searchFirst(): array
    {
        /** @var array<\App\Model\Entity\Log\PageAccessLog> $ormEntities */
        $ormEntities = $this->table
            ->find()
            ->contain(['AdminAccounts'])
            ->where(
                array_filter([
                    // Memo: 検索条件
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
            )
            ->orderBy([
                'PageAccessLogs.accessed' => 'DESC', 
                'PageAccessLogs.id' => 'DESC'
            ])
            ->limit($this->condition->getLimit() + 1) // 1件多く取得して、次ページの有無を判断する
            ->all()
            ->toArray();

        return array_map(
            fn($ormEntity): DomainEntity => $this->mapper->toDomainEntity($ormEntity),
            $ormEntities,
        );
    }

    /**
     * @return array<\App\Domain\Log\PageAccessLogs\Entity\PageAccessLog>
     */
    private function searchLast(): array
    {
        /** @var array<\App\Model\Entity\Log\PageAccessLog> $ormEntities */
        $ormEntities = $this->table
            ->find()
            ->contain(['AdminAccounts'])
            ->where(
                array_filter([
                    // Memo: 検索条件
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
            )
            ->orderBy([
                'PageAccessLogs.accessed' => 'ASC', 
                'PageAccessLogs.id' => 'ASC'
            ])
            ->limit($this->condition->getLimit() + 1) // 1件多く取得して、次ページの有無を判断する
            ->all()
            ->toArray();

        return array_reverse(
            array_map(
                fn($ormEntity): DomainEntity => $this->mapper->toDomainEntity($ormEntity),
                $ormEntities,
            )
        );
    }

    /**
     * @return array<\App\Domain\Log\PageAccessLogs\Entity\PageAccessLog>
     */
    private function searchNext(): array
    {
        /** @var array<\App\Model\Entity\Log\PageAccessLog> $ormEntities */
        $ormEntities = $this->table
            ->find()
            ->contain(['AdminAccounts'])
            ->where(
                array_filter([
                    'PageAccessLogs.accessed >='
                        => $this->condition->getCursorAccessed()->format('Y-m-d\TH:i:s'),
                    'PageAccessLogs.accessed >='
                        => $this->condition->getAccessedFrom()->format('Y-m-d\TH:i:s'),
                    'PageAccessLogs.accessed <='
                        => $this->condition->getAccessedTo()->format('Y-m-d\TH:i:s'),
                    'PageAccessLogs.account_id'
                        => $this->condition->getAccountId()->toStringOrNull(),
                    'PageAccessLogs.id >='
                        => $this->condition->getCursorId()->toStringOrNull(),
                    'PageAccessLogs.account_type'
                        => $this->condition->getAccountType()->toStringOrNull(),
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
            )
            ->orderBy([
                'PageAccessLogs.accessed' => 'DESC', 
                'PageAccessLogs.id' => 'DESC'
            ])
            ->limit($this->condition->getLimit() + 2) // 2件多く取得して、次ページの有無を判断する
            ->all()
            ->toArray();

        return array_map(
            fn($ormEntity): DomainEntity => $this->mapper->toDomainEntity($ormEntity),
            $ormEntities,
        );
    }

    /**
     * @return array<\App\Domain\Log\PageAccessLogs\Entity\PageAccessLog>
     */
    private function searchPrev(): array
    {
        /** @var array<\App\Model\Entity\Log\PageAccessLog> $ormEntities */
        $ormEntities = $this->table
            ->find()
            ->contain(['AdminAccounts'])
            ->where(
                array_filter([
                    // Memo: 検索条件
                    'PageAccessLogs.accessed >='
                        => $this->condition->getAccessedFrom()->format('Y-m-d\TH:i:s'),
                    'PageAccessLogs.accessed <='
                        => $this->condition->getAccessedTo()->format('Y-m-d\TH:i:s'),
                    'PageAccessLogs.accessed <='
                        => $this->condition->getCursorAccessed()->format('Y-m-d\TH:i:s'),
                    'PageAccessLogs.id <='
                        => $this->condition->getCursorId()->toStringOrNull(),
                    'PageAccessLogs.account_id'
                        => $this->condition->getAccountId()->toStringOrNull(),
                    'PageAccessLogs.account_type'
                        => $this->condition->getAccountType()->toStringOrNull(),
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
            )
            ->orderBy([
                'PageAccessLogs.accessed' => 'ASC', 
                'PageAccessLogs.id' => 'ASC'
            ])
            ->limit($this->condition->getLimit() + 2) // 2件多く取得して、前ページの有無を判断する
            ->all()
            ->toArray();

        return array_reverse(
            array_map(
                fn($ormEntity): DomainEntity => $this->mapper->toDomainEntity($ormEntity),
                $ormEntities,
            )
        );
    }
}
