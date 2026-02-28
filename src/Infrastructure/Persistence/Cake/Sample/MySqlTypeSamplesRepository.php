<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Cake\Sample;

use \Cake\Database\Expression\QueryExpression;
use \App\Domain\Sample\MySqlTypeSamples\Repository\MySqlTypeSamplesRepository as DomainMySqlTypeSamplesRepository;
use \App\Domain\Sample\MySqlTypeSamples\SearchCondition;

/**
 *
 */
class MySqlTypeSamplesRepository implements DomainMySqlTypeSamplesRepository
{
    /**
     * 
     * @var \App\Model\Table\Sample\MySqlTypeSamplesTable
     */
    private \App\Model\Table\Sample\MySqlTypeSamplesTable $mySqlTypeSamplesTable;

    public function __construct()
    {
        $this->mySqlTypeSamplesTable = \App\Model\Table\Sample\MySqlTypeSamplesTable::getInstance();
    }

    /**
     * 
     * @return \Cake\ORM\Query
     */
    public function getQuery(SearchCondition $condition): \Cake\ORM\Query
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
                'MySqlTypeSamples__json_col' => 'MySqlTypeSamples.json_col',
            ])
            ->where(array_filter([
                'MySqlTypeSamples.id' => $condition->getId(),
                'MySqlTypeSamples.int_col >=' => $condition->getIntColFrom(),
                'MySqlTypeSamples.int_col <=' => $condition->getIntColTo(),
                'MySqlTypeSamples.bigint_col >=' => $condition->getBigintColFrom(),
                'MySqlTypeSamples.bigint_col <=' => $condition->getBigintColTo(),
                'MySqlTypeSamples.decimal_col >=' => $condition->getDecimalColFrom(),
                'MySqlTypeSamples.decimal_col <=' => $condition->getDecimalColTo(),
                'MySqlTypeSamples.float_col >=' => $condition->getFloatColFrom(),
                'MySqlTypeSamples.float_col <=' => $condition->getFloatColTo(),
                'MySqlTypeSamples.double_col >=' => $condition->getDoubleColFrom(),
                'MySqlTypeSamples.double_col <=' => $condition->getDoubleColTo(),
                'MySqlTypeSamples.date_col >=' => $condition->getDateColFrom(),
                'MySqlTypeSamples.date_col <=' => $condition->getDateColTo(),
                'MySqlTypeSamples.time_col >=' => $condition->getTimeColFrom(),
                'MySqlTypeSamples.time_col <=' => $condition->getTimeColTo(),
                'MySqlTypeSamples.datetime_col >=' => $condition->getDatetimeColFrom(),
                'MySqlTypeSamples.datetime_col <=' => $condition->getDatetimeColTo(),
                $condition->getKeyword() ? new QueryExpression(
                        'MATCH(search_text) AGAINST(:keyword IN BOOLEAN MODE)'
                    ) : ":keyword IS NULL",

            ], fn($v) => $v !== null))
            ->bind(':keyword', $condition->getKeyword(), 'string');
    }
}