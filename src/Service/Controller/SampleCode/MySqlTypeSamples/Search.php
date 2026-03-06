<?php
declare(strict_types=1);

namespace App\Service\Controller\SampleCode\MySqlTypeSamples;

use App\Domain\Sample\MySqlTypeSamples\SearchCondition;
use App\Domain\Sample\MySqlTypeSamples\ValueObject;
use App\Infrastructure\Persistence\Cake\Sample\MySqlTypeSamplesRepository;
use App\Service\Controller\Shared\ServiceInterface;
use App\Service\Controller\Shared\ServiceTrait;
use App\Service\Input\Normalizer\RecursiveEmptyStringToNullNormalizer;
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
        $data = (new RecursiveEmptyStringToNullNormalizer())->normalize($this->request->getQuery());

        return (new MySqlTypeSamplesRepository())->search(new SearchCondition(
            id: new ValueObject\Id($data['id'] ?? null),
            intColFrom: new ValueObject\IntCol($data['int_col_from'] ?? null),
            intColTo: new ValueObject\IntCol($data['int_col_to'] ?? null),
            bigintColFrom: new ValueObject\BigintCol($data['bigint_col_from'] ?? null),
            bigintColTo: new ValueObject\BigintCol($data['bigint_col_to'] ?? null),
            decimalColFrom: new ValueObject\DecimalCol($data['decimal_col_from'] ?? null),
            decimalColTo: new ValueObject\DecimalCol($data['decimal_col_to'] ?? null),
            floatColFrom: new ValueObject\FloatCol($data['float_col_from'] ?? null),
            floatColTo: new ValueObject\FloatCol($data['float_col_to'] ?? null),
            doubleColFrom: new ValueObject\DoubleCol($data['double_col_from'] ?? null),
            doubleColTo: new ValueObject\DoubleCol($data['double_col_to'] ?? null),
            dateColFrom: new ValueObject\DateCol($data['date_col_from'] ?? null),
            dateColTo: new ValueObject\DateCol($data['date_col_to'] ?? null),
            timeColFrom: new ValueObject\TimeCol($data['time_col_from'] ?? null),
            timeColTo: new ValueObject\TimeCol($data['time_col_to'] ?? null),
            datetimeColFrom: new ValueObject\DatetimeCol($data['datetime_col_from'] ?? null),
            datetimeColTo: new ValueObject\DatetimeCol($data['datetime_col_to'] ?? null),
            keyword: new ValueObject\Search\Keyword($data['keyword'] ?? null),
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
