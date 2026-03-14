<?php
declare(strict_types=1);

namespace App\Service\Controller\SampleCode\MySqlTypeSamples;

use App\Domain\Sample\MySqlTypeSamples\SearchCondition;
use App\Domain\Sample\MySqlTypeSamples\ValueObject;
use App\Infrastructure\Persistence\Cake\Sample\MySqlTypeSamplesRepository;
use App\Service\Controller\Shared\ServiceInterface;
use App\Service\Controller\Shared\ServiceTrait;
use Cake\ORM\Query;

final class Search implements ServiceInterface
{
    use ServiceTrait;

    /**
     * @return array<string, array<int, string>>
     */
    public function getInitParams(): array
    {
        return [
            'show_fields' => [
                'col_id',
                'col_int',
                'col_date',
                'col_char',
                'col_varchar',
            ],
        ];
    }

    /**
     * @return \Cake\ORM\Query
     */
    public function getSearchQuery(): Query
    {
        /** @var array<string, string> $data */
        $data = $this->request->getQuery();

        return (new MySqlTypeSamplesRepository())->search(new SearchCondition(
            id: ValueObject\Id::fromString($data['id'] ?? null),
            intColFrom: ValueObject\IntCol::fromString($data['int_col_from'] ?? null),
            intColTo: ValueObject\IntCol::fromString($data['int_col_to'] ?? null),
            bigintColFrom: ValueObject\BigintCol::fromString($data['bigint_col_from'] ?? null),
            bigintColTo: ValueObject\BigintCol::fromString($data['bigint_col_to'] ?? null),
            decimalColFrom: ValueObject\DecimalCol::fromString($data['decimal_col_from'] ?? null),
            decimalColTo: ValueObject\DecimalCol::fromString($data['decimal_col_to'] ?? null),
            floatColFrom: ValueObject\FloatCol::fromString($data['float_col_from'] ?? null),
            floatColTo: ValueObject\FloatCol::fromString($data['float_col_to'] ?? null),
            doubleColFrom: ValueObject\DoubleCol::fromString($data['double_col_from'] ?? null),
            doubleColTo: ValueObject\DoubleCol::fromString($data['double_col_to'] ?? null),
            dateColFrom: ValueObject\DateCol::fromString($data['date_col_from'] ?? null),
            dateColTo: ValueObject\DateCol::fromString($data['date_col_to'] ?? null),
            timeColFrom: ValueObject\TimeCol::fromString($data['time_col_from'] ?? null),
            timeColTo: ValueObject\TimeCol::fromString($data['time_col_to'] ?? null),
            datetimeColFrom: ValueObject\DatetimeCol::fromString($data['datetime_col_from'] ?? null),
            datetimeColTo: ValueObject\DatetimeCol::fromString($data['datetime_col_to'] ?? null),
            keyword: ValueObject\Search\Keyword::fromString($data['keyword'] ?? null),
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
