<?php
declare(strict_types=1);

namespace App\Service\Controller\Sample;

use \App\Security\Input\Cast;
use \Cake\Database\Expression\QueryExpression;

/**
 *
 */
class Search implements \App\Service\Controller\ServiceInterface
{
    use \App\Service\Controller\ServiceTrait;

    /**
     * 
     * @var \App\Model\Table\Sample\MySqlTypeSamplesTable
     */
    private \App\Model\Table\Sample\MySqlTypeSamplesTable $mySqlTypeSamplesTable;

    private function __construct()
    {
        $this->mySqlTypeSamplesTable = \App\Model\Table\Sample\MySqlTypeSamplesTable::getInstance();
    }

    /**
     * 
     * @return array
     */
    public function getInitParams() : array
    {
        return [];
    }

    /**
     * 
     * @return \Cake\ORM\Query
     */
    public function getSearchQuery() : \Cake\ORM\Query
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
                'MySqlTypeSamples.id' => Cast::toString($this->request->getQuery('id')),
                'MySqlTypeSamples.int_col >=' => Cast::toInt($this->request->getQuery('int_col_from')),
                'MySqlTypeSamples.int_col <=' => Cast::toInt($this->request->getQuery('int_col_to')),
                'MySqlTypeSamples.bigint_col >=' => Cast::toInt($this->request->getQuery('bigint_col_from')),
                'MySqlTypeSamples.bigint_col <=' => Cast::toInt($this->request->getQuery('bigint_col_to')),
                'MySqlTypeSamples.decimal_col >=' => Cast::toFloat($this->request->getQuery('decimal_col_from')),
                'MySqlTypeSamples.decimal_col <=' => Cast::toFloat($this->request->getQuery('decimal_col_to')),
                'MySqlTypeSamples.float_col >=' => Cast::toFloat($this->request->getQuery('float_col_from')),
                'MySqlTypeSamples.float_col <=' => Cast::toFloat($this->request->getQuery('float_col_to')),
                'MySqlTypeSamples.double_col >=' => Cast::toFloat($this->request->getQuery('double_col_from')),
                'MySqlTypeSamples.double_col <=' => Cast::toFloat($this->request->getQuery('double_col_to')),
                'MySqlTypeSamples.date_col >=' => Cast::toDate($this->request->getQuery('date_col_from')),
                'MySqlTypeSamples.date_col <=' => Cast::toDate($this->request->getQuery('date_col_to')),
                'MySqlTypeSamples.time_col >=' => Cast::toTime($this->request->getQuery('time_col_from')),
                'MySqlTypeSamples.time_col <=' => Cast::toTime($this->request->getQuery('time_col_to')),
                'MySqlTypeSamples.datetime_col >=' => Cast::toDateTime($this->request->getQuery('datetime_col_from')),
                'MySqlTypeSamples.datetime_col <=' => Cast::toDateTime($this->request->getQuery('datetime_col_to')),
                $this->request->getQuery('keyword') ? new QueryExpression(
                        'MATCH(search_text) AGAINST(:kw IN BOOLEAN MODE)'
                    ) : ":kw = ''",

            ]))
            ->bind(':kw', $this->request->getQuery('keyword'), 'string');
            ;
    }

    /**
     * 
     * @return array
     */
    public function getSearchSettings() : array
    {
        return [
            'limit' => 20,
            'maxLimit' => 200,
            'sortableFields' => [
                'id',
                'int_col',
                'bigint_col',
                'decimal_col',
                'float_col',
                'double_col',
                'date_col',
                'time_col',
                'datetime_col',
                'char_col',
                'varchar_col',
                'json_col',
            ],
            'order' => [
                'id' => 'DESC'
            ],
        ];
    }
}