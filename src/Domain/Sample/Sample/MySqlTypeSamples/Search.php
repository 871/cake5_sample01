<?php
declare(strict_types=1);

namespace App\Domain\Sample\Sample\MySqlTypeSamples;

use \Cake\Database\Expression\QueryExpression;

/**
 *
 */
class Search
{
    /**
     * 
     * @var \App\Model\Table\Sample\MySqlTypeSamplesTable
     */
    private \App\Model\Table\Sample\MySqlTypeSamplesTable $mySqlTypeSamplesTable;

    public function __construct(
        private SearchConditionInterface $condition
    ) {
        $this->mySqlTypeSamplesTable = \App\Model\Table\Sample\MySqlTypeSamplesTable::getInstance();
    }

    /**
     * 
     * @return \Cake\ORM\Query
     */
    public function getQuery() : \Cake\ORM\Query
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
                'MySqlTypeSamples.id' => $this->condition->getId(),
                'MySqlTypeSamples.int_col >=' => $this->condition->getIntColFrom(),
                'MySqlTypeSamples.int_col <=' => $this->condition->getIntColTo(),
                'MySqlTypeSamples.bigint_col >=' => $this->condition->getBigintColFrom(),
                'MySqlTypeSamples.bigint_col <=' => $this->condition->getBigintColTo(),
                'MySqlTypeSamples.decimal_col >=' => $this->condition->getDecimalColFrom(),
                'MySqlTypeSamples.decimal_col <=' => $this->condition->getDecimalColTo(),
                'MySqlTypeSamples.float_col >=' => $this->condition->getFloatColFrom(),
                'MySqlTypeSamples.float_col <=' => $this->condition->getFloatColTo(),
                'MySqlTypeSamples.double_col >=' => $this->condition->getDoubleColFrom(),
                'MySqlTypeSamples.double_col <=' => $this->condition->getDoubleColTo(),
                'MySqlTypeSamples.date_col >=' => $this->condition->getDateColFrom(),
                'MySqlTypeSamples.date_col <=' => $this->condition->getDateColTo(),
                'MySqlTypeSamples.time_col >=' => $this->condition->getTimeColFrom(),
                'MySqlTypeSamples.time_col <=' => $this->condition->getTimeColTo(),
                'MySqlTypeSamples.datetime_col >=' => $this->condition->getDatetimeColFrom(),
                'MySqlTypeSamples.datetime_col <=' => $this->condition->getDatetimeColTo(),
                $this->condition->getKeyword() ? new QueryExpression(
                        'MATCH(search_text) AGAINST(:keyword IN BOOLEAN MODE)'
                    ) : ":keyword IS NULL",

            ], fn($v) => $v !== null))
            ->bind(':keyword', $this->condition->getKeyword(), 'string');
            ;
    }
}