<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Cake\Sample;

use App\Domain\Sample\MySqlTypeSamples\Entity\MySqlTypeSample as DomainEntity;
use App\Domain\Sample\MySqlTypeSamples\Repository\MySqlTypeSamplesRepository as DomainMySqlTypeSamplesRepository;
use App\Domain\Sample\MySqlTypeSamples\SearchCondition;
use App\Model\Table\Sample\MySqlTypeSamplesTable;
use App\Service\Input\Normalizer\RecursiveEmptyStringToNullNormalizer;
use Cake\Database\Expression\QueryExpression;
use Cake\ORM\Query;

class MySqlTypeSamplesRepository implements DomainMySqlTypeSamplesRepository
{
    /**
     * @var \App\Model\Table\Sample\MySqlTypeSamplesTable
     */
    private MySqlTypeSamplesTable $mySqlTypeSamplesTable;

    /**
     * @return void
     */
    public function __construct()
    {
        $this->mySqlTypeSamplesTable = MySqlTypeSamplesTable::getInstance();
    }

    /**
     * @param \App\Domain\Sample\MySqlTypeSamples\SearchCondition $condition
     * @return \Cake\ORM\Query
     */
    public function search(SearchCondition $condition): Query
    {
        return $this->mySqlTypeSamplesTable
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
                    'MySqlTypeSamples.id' => $condition->getId()->toString(),
                    'MySqlTypeSamples.int_col >=' => $condition->getIntColFrom()->toString(),
                    'MySqlTypeSamples.int_col <=' => $condition->getIntColTo()->toString(),
                    'MySqlTypeSamples.bigint_col >=' => $condition->getBigintColFrom()->toString(),
                    'MySqlTypeSamples.bigint_col <=' => $condition->getBigintColTo()->toString(),
                    'MySqlTypeSamples.decimal_col >=' => $condition->getDecimalColFrom()->toString(),
                    'MySqlTypeSamples.decimal_col <=' => $condition->getDecimalColTo()->toString(),
                    'MySqlTypeSamples.float_col >=' => $condition->getFloatColFrom()->toString(),
                    'MySqlTypeSamples.float_col <=' => $condition->getFloatColTo()->toString(),
                    'MySqlTypeSamples.double_col >=' => $condition->getDoubleColFrom()->toString(),
                    'MySqlTypeSamples.double_col <=' => $condition->getDoubleColTo()->toString(),
                    'MySqlTypeSamples.date_col >=' => $condition->getDateColFrom()->toString(),
                    'MySqlTypeSamples.date_col <=' => $condition->getDateColTo()->toString(),
                    'MySqlTypeSamples.time_col >=' => $condition->getTimeColFrom()->toString(),
                    'MySqlTypeSamples.time_col <=' => $condition->getTimeColTo()->toString(),
                    'MySqlTypeSamples.datetime_col >=' => $condition->getDatetimeColFrom()->toString(),
                    'MySqlTypeSamples.datetime_col <=' => $condition->getDatetimeColTo()->toString(),
                    $condition->getKeyword()->toString() !== '' ? new QueryExpression(
                        'MATCH(MySqlTypeSamples.search_text) AGAINST(:keyword IN BOOLEAN MODE)',
                    ) : new QueryExpression(":keyword = ''"),
                ], fn($v) => $v !== ''),
            )
            ->bind(':keyword', $condition->getKeyword()->toString(), 'string')
            ->formatResults(function ($results) {
                return $results->map(function ($entity) {
                    /** @var array<string, ?string> $data */
                    $data = (array)(new RecursiveEmptyStringToNullNormalizer())->normalize($entity->toArray());

                    return new DomainEntity(...$data);
                });
            });
    }
}
