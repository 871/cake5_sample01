<?php
declare(strict_types=1);

namespace App\Service\Controller\Sample\MySqlTypeSamples;

use App\Domain\Sample\MySqlTypeSamples\SearchCondition;
use App\Infrastructure\Persistence\Cake\Sample\MySqlTypeSamplesRepository;
use App\Security\Input\Cast;
use App\Service\Controller\Shared\ServiceInterface;
use App\Service\Controller\Shared\ServiceTrait;
use Cake\ORM\Query;

final class Search implements ServiceInterface
{
    use ServiceTrait;

    /**
     * @return array<string>
     */
    public function getInitParams(): array
    {
        return [];
    }

    /**
     * @return \Cake\ORM\Query
     */
    public function getSearchQuery(): Query
    {
        $repository = new MySqlTypeSamplesRepository();

        return $repository->search(new SearchCondition(
            id: Cast::toString($this->request->getQuery('id')),
            intColFrom: Cast::toInt($this->request->getQuery('int_col_from')),
            intColTo: Cast::toInt($this->request->getQuery('int_col_to')),
            bigintColFrom: Cast::toInt($this->request->getQuery('bigint_col_from')),
            bigintColTo: Cast::toInt($this->request->getQuery('bigint_col_to')),
            decimalColFrom: Cast::toFloat($this->request->getQuery('decimal_col_from')),
            decimalColTo: Cast::toFloat($this->request->getQuery('decimal_col_to')),
            floatColFrom: Cast::toFloat($this->request->getQuery('float_col_from')),
            floatColTo: Cast::toFloat($this->request->getQuery('float_col_to')),
            doubleColFrom: Cast::toFloat($this->request->getQuery('double_col_from')),
            doubleColTo: Cast::toFloat($this->request->getQuery('double_col_to')),
            dateColFrom: Cast::toDate($this->request->getQuery('date_col_from')),
            dateColTo: Cast::toDate($this->request->getQuery('date_col_to')),
            timeColFrom: Cast::toTime($this->request->getQuery('time_col_from')),
            timeColTo: Cast::toTime($this->request->getQuery('time_col_to')),
            datetimeColFrom: Cast::toDateTime($this->request->getQuery('datetime_col_from')),
            datetimeColTo: Cast::toDateTime($this->request->getQuery('datetime_col_to')),
            keyword: Cast::toString($this->request->getQuery('keyword')),
        ));
    }

    /**
     * @return array<string, array<int|string, string>|int>
     */
    public function getPaginateSettings(): array
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
                'id' => 'DESC',
            ],
        ];
    }
}
