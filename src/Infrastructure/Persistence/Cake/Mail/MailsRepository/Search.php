<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Cake\Mail\MailsRepository;

use App\Domain\Mail\Entity\Mail as DomainEntity;
use App\Domain\Mail\SearchCondition;
use App\Domain\Mail\ValueObject\Search\NavigationType;
use App\Infrastructure\Persistence\Cake\Mail\MailMapper;
use App\Model\Table\Mail\MailsTable;
use Cake\Database\Expression\QueryExpression;
use Cake\ORM\Locator\LocatorAwareTrait;
use LogicException;

final class Search
{
    use LocatorAwareTrait;

    /**
     * @var \App\Model\Table\Mail\MailsTable
     */
    private MailsTable $table;

    /**
     * @var \App\Infrastructure\Persistence\Cake\Mail\MailMapper
     */
    private MailMapper $mapper;

    /**
     * @param \App\Domain\Mail\SearchCondition $condition
     */
    public function __construct(
        private readonly SearchCondition $condition,
    ) {
        $this->table = $this->fetchTable(MailsTable::class);
        $this->mapper = new MailMapper();
    }

    /**
     * @return array<\App\Domain\Mail\Entity\Mail>
     */
    public function run(): array
    {
        return match ($this->condition->getNavigationType()->toString()) {
            NavigationType::FIRST => $this->searchFirst(),
            NavigationType::LAST => $this->searchLast(),
            NavigationType::NEXT => $this->searchNext(),
            NavigationType::PREV => $this->searchPrev(),
            default => throw new LogicException(
                'Unknown navigation type: ' . $this->condition->getNavigationType()->toString(),
            ),
        };
    }

    /**
     * @return array<\App\Domain\Mail\Entity\Mail>
     */
    private function searchFirst(): array
    {
        $keyword = trim($this->condition->getKeyword()?->toString() ?? '');

        $query = $this->table
            ->find()
            ->where(
                array_filter([
                    'Mails.send_scheduled_at >='
                        => $this->condition->getSendScheduledAtFrom()->format('Y-m-d\TH:i:s'),
                    'Mails.send_scheduled_at <='
                        => $this->condition->getSendScheduledAtTo()->format('Y-m-d\TH:i:s'),
                    'Mails.send_status'
                        => $this->condition->getSendStatus()?->toString(),
                    'Mails.related_data_key'
                        => $this->condition->getRelatedDataKey()->toStringOrNull(),
                ], fn($v) => !in_array($v, [null, '', []], true)),
            )
            ->orderBy([
                'Mails.id' => 'ASC',
            ])
            ->limit($this->condition->getLimit());

        if ($keyword !== '') {
            $query
                ->where(new QueryExpression(
                    'MATCH(Mails.search_text) AGAINST(:keyword IN NATURAL LANGUAGE MODE)',
                ))
                ->bind(':keyword', $keyword, 'string');
        }

        /** @var array<\App\Model\Entity\Mail\Mail> $ormEntities */
        $ormEntities = $query->all()->toArray();

        return array_map(
            fn($ormEntity): DomainEntity => $this->mapper->toDomainEntity($ormEntity),
            $ormEntities,
        );
    }

    /**
     * @return array<\App\Domain\Mail\Entity\Mail>
     */
    private function searchLast(): array
    {
        $keyword = trim($this->condition->getKeyword()?->toString() ?? '');

        $query = $this->table
            ->find()
            ->where(
                array_filter([
                    'Mails.send_scheduled_at >='
                        => $this->condition->getSendScheduledAtFrom()->format('Y-m-d\TH:i:s'),
                    'Mails.send_scheduled_at <='
                        => $this->condition->getSendScheduledAtTo()->format('Y-m-d\TH:i:s'),
                    'Mails.send_status'
                        => $this->condition->getSendStatus()?->toString(),
                    'Mails.related_data_key'
                        => $this->condition->getRelatedDataKey()->toStringOrNull(),
                ], fn($v) => !in_array($v, [null, '', []], true)),
            )
            ->orderBy([
                'Mails.id' => 'DESC',
            ])
            ->limit($this->condition->getLimit());

        if ($keyword !== '') {
            $query
                ->where(new QueryExpression(
                    'MATCH(Mails.search_text) AGAINST(:keyword IN NATURAL LANGUAGE MODE)',
                ))
                ->bind(':keyword', $keyword, 'string');
        }

        /** @var array<\App\Model\Entity\Mail\Mail> $ormEntities */
        $ormEntities = $query->all()->toArray();

        return array_reverse( // Memo: LASTのときは逆順で取得しているため、元の順序に戻す
            array_map(
                fn($ormEntity): DomainEntity => $this->mapper->toDomainEntity($ormEntity),
                $ormEntities,
            ),
        );
    }

    /**
     * @return array<\App\Domain\Mail\Entity\Mail>
     */
    private function searchNext(): array
    {
        $keyword = trim($this->condition->getKeyword()?->toString() ?? '');

        $query = $this->table
            ->find()
            ->where(
                array_filter([
                    'Mails.id >'
                        => $this->condition->getSearchKey()->toString(),
                    // Memo: 検索条件
                    'Mails.send_scheduled_at >='
                        => $this->condition->getSendScheduledAtFrom()->format('Y-m-d\TH:i:s'),
                    'Mails.send_scheduled_at <='
                        => $this->condition->getSendScheduledAtTo()->format('Y-m-d\TH:i:s'),
                    'Mails.send_status'
                        => $this->condition->getSendStatus()?->toString(),
                    'Mails.related_data_key'
                        => $this->condition->getRelatedDataKey()->toStringOrNull(),
                ], fn($v) => !in_array($v, [null, '', []], true)),
            )
            ->orderBy([
                'Mails.id' => 'ASC',
            ])
            ->limit($this->condition->getLimit());

        if ($keyword !== '') {
            $query
                ->where(new QueryExpression(
                    'MATCH(Mails.search_text) AGAINST(:keyword IN NATURAL LANGUAGE MODE)',
                ))
                ->bind(':keyword', $keyword, 'string');
        }

        /** @var array<\App\Model\Entity\Mail\Mail> $ormEntities */
        $ormEntities = $query->all()->toArray();

        return array_map(
            fn($ormEntity): DomainEntity => $this->mapper->toDomainEntity($ormEntity),
            $ormEntities,
        );
    }

    /**
     * @return array<\App\Domain\Mail\Entity\Mail>
     */
    private function searchPrev(): array
    {
        $keyword = trim($this->condition->getKeyword()?->toString() ?? '');

        $query = $this->table
            ->find()
            ->where(
                array_filter([
                    'Mails.id <'
                        => $this->condition->getSearchKey()->toString(),
                    // Memo: 検索条件
                    'Mails.send_scheduled_at >='
                        => $this->condition->getSendScheduledAtFrom()->format('Y-m-d\TH:i:s'),
                    'Mails.send_scheduled_at <='
                        => $this->condition->getSendScheduledAtTo()->format('Y-m-d\TH:i:s'),
                    'Mails.send_status'
                        => $this->condition->getSendStatus()?->toString(),
                    'Mails.related_data_key'
                        => $this->condition->getRelatedDataKey()->toStringOrNull(),
                ], fn($v) => !in_array($v, [null, '', []], true)),
            )
            ->orderBy([
                'Mails.id' => 'DESC',
            ])
            ->limit($this->condition->getLimit());

        if ($keyword !== '') {
            $query
                ->where(new QueryExpression(
                    'MATCH(Mails.search_text) AGAINST(:keyword IN NATURAL LANGUAGE MODE)',
                ))
                ->bind(':keyword', $keyword, 'string');
        }

        /** @var array<\App\Model\Entity\Mail\Mail> $ormEntities */
        $ormEntities = $query->all()->toArray();

        return array_reverse( // Memo: PREVのときは逆順で取得しているため、元の順序に戻す
            array_map(
                fn($ormEntity): DomainEntity => $this->mapper->toDomainEntity($ormEntity),
                $ormEntities,
            ),
        );
    }
}
