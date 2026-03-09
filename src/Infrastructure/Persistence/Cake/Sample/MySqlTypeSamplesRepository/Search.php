<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Cake\Sample\MySqlTypeSamplesRepository;

use App\Domain\Sample\MySqlTypeSamples\Entity\MySqlTypeSample as DomainEntity;
use App\Domain\Sample\MySqlTypeSamples\SearchCondition;
use App\Model\Table\Sample\MySqlTypeSamplesTable;
use App\Service\Input\Normalizer\RecursiveEmptyStringToNullNormalizer;
use Cake\Database\Expression\QueryExpression;
use Cake\ORM\Locator\LocatorAwareTrait;
use Cake\ORM\Query;

final class Search
{
    use LocatorAwareTrait;

    /**
     * @var \App\Model\Table\Sample\MySqlTypeSamplesTable
     */
    private MySqlTypeSamplesTable $table;

    /**
     * @param \App\Domain\Sample\MySqlTypeSamples\SearchCondition $condition
     */
    public function __construct(
        private readonly SearchCondition $condition,
    ) {
        $this->table = $this->fetchTable(MySqlTypeSamplesTable::class);
    }

    /**
     * @return \Cake\ORM\Query
     */
    public function run(): Query
    {
        return $this->table
            ->find()
            ->select([
                'MySqlTypeSamples__id' => 'MySqlTypeSamples.id',
                'MySqlTypeSamples__int_col' => 'MySqlTypeSamples.int_col',
                'MySqlTypeSamples__bigint_col' => 'MySqlTypeSamples.bigint_col',
                'MySqlTypeSamples__decimal_col' => 'MySqlTypeSamples.decimal_col',
                'MySqlTypeSamples__float_col' => 'MySqlTypeSamples.float_col',
                'MySqlTypeSamples__double_col' => 'MySqlTypeSamples.double_col',
                'MySqlTypeSamples__date_col' => 'MySqlTypeSamples.date_col',
                'MySqlTypeSamples__time_col' => 'MySqlTypeSamples.time_col',
                'MySqlTypeSamples__datetime_col' => 'MySqlTypeSamples.datetime_col',
                'MySqlTypeSamples__char_col' => 'MySqlTypeSamples.char_col',
                'MySqlTypeSamples__varchar_col' => 'MySqlTypeSamples.varchar_col',
                'MySqlTypeSamples__text_col' => 'MySqlTypeSamples.text_col',
                'MySqlTypeSamples__mediumtext_col' => 'MySqlTypeSamples.mediumtext_col',
                'MySqlTypeSamples__longtext_col' => 'MySqlTypeSamples.longtext_col',
                'MySqlTypeSamples__json_col' => 'MySqlTypeSamples.json_col',
            ])
            ->where(
                array_filter([
                    'MySqlTypeSamples.id' => $this->condition->getId()->toString(),
                    'MySqlTypeSamples.int_col >=' => $this->condition->getIntColFrom()->toString(),
                    'MySqlTypeSamples.int_col <=' => $this->condition->getIntColTo()->toString(),
                    'MySqlTypeSamples.bigint_col >=' => $this->condition->getBigintColFrom()->toString(),
                    'MySqlTypeSamples.bigint_col <=' => $this->condition->getBigintColTo()->toString(),
                    'MySqlTypeSamples.decimal_col >=' => $this->condition->getDecimalColFrom()->toString(),
                    'MySqlTypeSamples.decimal_col <=' => $this->condition->getDecimalColTo()->toString(),
                    'MySqlTypeSamples.float_col >=' => $this->condition->getFloatColFrom()->toString(),
                    'MySqlTypeSamples.float_col <=' => $this->condition->getFloatColTo()->toString(),
                    'MySqlTypeSamples.double_col >=' => $this->condition->getDoubleColFrom()->toString(),
                    'MySqlTypeSamples.double_col <=' => $this->condition->getDoubleColTo()->toString(),
                    'MySqlTypeSamples.date_col >=' => $this->condition->getDateColFrom()->toString(),
                    'MySqlTypeSamples.date_col <=' => $this->condition->getDateColTo()->toString(),
                    'MySqlTypeSamples.time_col >=' => $this->condition->getTimeColFrom()->toString(),
                    'MySqlTypeSamples.time_col <=' => $this->condition->getTimeColTo()->toString(),
                    'MySqlTypeSamples.datetime_col >=' => $this->condition->getDatetimeColFrom()->toString(),
                    'MySqlTypeSamples.datetime_col <=' => $this->condition->getDatetimeColTo()->toString(),
                    $this->condition->getKeyword()->toString() !== '' ? new QueryExpression(
                        'MATCH(MySqlTypeSamples.search_text) AGAINST(:keyword IN BOOLEAN MODE)',
                    ) : new QueryExpression(":keyword = ''"),
                ], fn($v) => $v !== ''),
            )
            ->bind(':keyword', $this->condition->getKeyword()->toString(), 'string')
            ->formatResults(function ($results) {
                return $results->map(function ($entity) {
                    /** @var array<string, ?string> $data */
                    $data = (array)(new RecursiveEmptyStringToNullNormalizer())->normalize($entity->toArray());
                    // Memo: 実装的にはやりすぎ感はあるがTEST的に試す
                    return new DomainEntity(...$data);
                });
            });
    }
}
