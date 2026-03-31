<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Cake\Log\PageAccessLogs\PageAccessLogsRepository;

use App\Domain\Log\PageAccessLogs\Entity\PageAccessLog as DomainEntity;
use App\Domain\Log\PageAccessLogs\SearchCondition;
use App\Domain\Log\PageAccessLogs\ValueObject\NavigationType;
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
        $navigationType = $this->condition->getNavigationType()->toString();
        $isReverse = in_array($navigationType, [NavigationType::LAST, NavigationType::PREV], true);

        $query = $this->table
            ->find()
            ->contain(['AdminAccounts'])
            ->where(
                array_filter([
                    'PageAccessLogs.accessed >='
                        => $this->condition->getAccessedFrom()->format('Y-m-d\TH:i:s.u'),
                    'PageAccessLogs.accessed <='
                        => $this->condition->getAccessedTo()->format('Y-m-d\TH:i:s.u'),
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

        $cursorId = $this->condition->getCursorId()->toStringOrNull();
        $cursorAccessed = $this->condition->getCursorAccessed()->format('Y-m-d\TH:i:s.u');

        if ($navigationType === NavigationType::NEXT && $cursorId !== null && $cursorAccessed !== null) {
            $query->where([
                'OR' => [
                    ['PageAccessLogs.accessed >' => $cursorAccessed],
                    [
                        'PageAccessLogs.accessed' => $cursorAccessed,
                        'PageAccessLogs.id >' => $cursorId,
                    ],
                ],
            ]);
        } elseif ($navigationType === NavigationType::PREV && $cursorId !== null && $cursorAccessed !== null) {
            $query->where([
                'OR' => [
                    ['PageAccessLogs.accessed <' => $cursorAccessed],
                    [
                        'PageAccessLogs.accessed' => $cursorAccessed,
                        'PageAccessLogs.id <' => $cursorId,
                    ],
                ],
            ]);
        }

        if ($isReverse) {
            $query->orderBy(['PageAccessLogs.accessed' => 'DESC', 'PageAccessLogs.id' => 'DESC']);
        } else {
            $query->orderBy(['PageAccessLogs.accessed' => 'ASC', 'PageAccessLogs.id' => 'ASC']);
        }

        $query->limit($this->condition->getLimit());

        /** @var array<\App\Model\Entity\Log\PageAccessLog> $ormEntities */
        $ormEntities = $query->all()->toArray();

        $domainEntities = array_map(
            fn($ormEntity): DomainEntity => $this->mapper->toDomainEntity($ormEntity),
            $ormEntities,
        );

        if ($isReverse) {
            $domainEntities = array_reverse($domainEntities);
        }

        return $domainEntities;
    }
}
