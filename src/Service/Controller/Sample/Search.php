<?php
declare(strict_types=1);

namespace App\Service\Controller\Sample;

use \App\Security\Input\Cast;
use \App\Domain\Sample\Sample\MySqlTypeSamples\Search as MySqlTypeSamplesSearch;
use \App\Domain\Sample\Sample\MySqlTypeSamples\SearchConditionInterface as MySqlTypeSamplesSearchConditionInterface;

/**
 *
 */
final class Search implements \App\Service\Controller\ServiceInterface
{
    use \App\Service\Controller\ServiceTrait;

    private function __construct()
    {
    
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
        // Memo: Cake5のController::paginate()の仕様を優先した設計とするため、Cake\ORM\Queryを直接返す形にしています。
        // 完全なDDDへ再設計する場合は、ドメインサービス内でページネーションやソートの処理も完結させる形にすることも検討してください。
        return (new MySqlTypeSamplesSearch(
            new class (
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
            ) implements MySqlTypeSamplesSearchConditionInterface {
                public function __construct(
                    private ?string $id,
                    private ?int $intColFrom,
                    private ?int $intColTo,
                    private ?int $bigintColFrom,
                    private ?int $bigintColTo,
                    private ?float $decimalColFrom,
                    private ?float $decimalColTo,
                    private ?float $floatColFrom,
                    private ?float $floatColTo,
                    private ?float $doubleColFrom,
                    private ?float $doubleColTo,
                    private ?\DateTimeInterface $dateColFrom,
                    private ?\DateTimeInterface $dateColTo,
                    private ?\DateTimeInterface $timeColFrom,
                    private ?\DateTimeInterface $timeColTo,
                    private ?\DateTimeInterface $datetimeColFrom,
                    private ?\DateTimeInterface $datetimeColTo,
                    private ?string $keyword
                ) {}

                public function getId(): ?string
                {
                    return $this->id;
                }
                public function getIntColFrom(): ?int
                {
                    return $this->intColFrom;
                }
                public function getIntColTo(): ?int
                {
                    return $this->intColTo;
                }
                public function getBigintColFrom(): ?int
                {
                    return $this->bigintColFrom;
                }
                public function getBigintColTo(): ?int
                {
                    return $this->bigintColTo;
                }
                public function getDecimalColFrom(): ?float
                {
                    return $this->decimalColFrom;
                }
                public function getDecimalColTo(): ?float
                {
                    return $this->decimalColTo;
                }
                public function getFloatColFrom(): ?float
                {
                    return $this->floatColFrom;
                }
                public function getFloatColTo(): ?float
                {
                    return $this->floatColTo;
                }
                public function getDoubleColFrom(): ?float
                {
                    return $this->doubleColFrom;
                }
                public function getDoubleColTo(): ?float
                {
                    return $this->doubleColTo;
                }
                public function getDateColFrom(): ?\DateTimeInterface
                {
                    return $this->dateColFrom;
                }
                public function getDateColTo(): ?\DateTimeInterface
                {
                    return $this->dateColTo;
                }
                public function getTimeColFrom(): ?\DateTimeInterface
                {
                    return $this->timeColFrom;
                }
                public function getTimeColTo(): ?\DateTimeInterface
                {
                    return $this->timeColTo;
                }
                public function getDatetimeColFrom(): ?\DateTimeInterface
                {
                    return $this->datetimeColFrom;
                }
                public function getDatetimeColTo(): ?\DateTimeInterface
                {
                    return $this->datetimeColTo;
                }
                public function getKeyword(): ?string
                {
                    return $this->keyword;
                }
            }
        ))->getQuery();
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