<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Cake\Mail\MailsRepository;

use App\Domain\Mail\Entity\Mail as DomainEntity;
use App\Domain\Mail\SearchCondition;
use App\Infrastructure\Persistence\Cake\Mail\MailMapper;
use App\Model\Table\Mail\MailsTable;
use Cake\Database\Expression\QueryExpression;
use Cake\ORM\Locator\LocatorAwareTrait;

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
                'Mails.send_scheduled_at' => 'ASC',
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
        $ormEntities = $query
            ->all()
            ->toArray();

        return array_map(
            fn($ormEntity): DomainEntity => $this->mapper->toDomainEntity($ormEntity),
            $ormEntities,
        );
    }
}
