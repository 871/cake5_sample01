<?php
declare(strict_types=1);

namespace App\Service\Controller\Sample;

use \Cake\Datasource\Paging\PaginatedInterface;
use \App\Security\Input\Cast;
use \App\Domain\Sample\Sample\MySqlTypeSamples\Search as MySqlTypeSamplesSearch;
use \App\Domain\Sample\Sample\MySqlTypeSamples\SearchConditionInterface as MySqlTypeSamplesSearchConditionInterface;
use \App\Infrastructure\Cake\CakePaginatorAdapter;

/**
 *
 */
final class Search implements \App\Service\Controller\Shared\ServiceInterface
{
    use \App\Service\Controller\Shared\ServiceTrait;

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
     * @return PaginatedInterface
     */
    public function getSearchResults() : PaginatedInterface
    {
        return $this->controller->paginate(
            $this->getSearchQuery(),
            $this->getSearchSettings()
        );
    }

    /**
     * 
     * @return \Cake\ORM\Query
     */
    private function getSearchQuery() : \Cake\ORM\Query
    {
        // Memo: Cake5のController::paginate()の仕様を優先した設計とするため、Cake\ORM\Queryを直接返す形にしています。
        // 完全なDDDへ再設計する場合は、ドメインサービス内でページネーションやソートの処理も完結させる形にすることも検討してください。
        return (new MySqlTypeSamplesSearch(
            new class (
                paginator: $this->controller,
                request: $this->request,
            ) implements MySqlTypeSamplesSearchConditionInterface {
                public function __construct(
                    private CakePaginatorAdapter $paginator,
                    private \Cake\Http\ServerRequest $request
                ) {}
                public function getPaginator(): CakePaginatorAdapter
                {
                    return $this->paginator;
                }
                public function getId(): ?string
                {
                    return Cast::toString($this->request->getQuery('id'));
                }
                public function getIntColFrom(): ?int
                {
                    return Cast::toInt($this->request->getQuery('int_col_from'));
                }
                public function getIntColTo(): ?int
                {
                    return Cast::toInt($this->request->getQuery('int_col_to'));
                }
                public function getBigintColFrom(): ?int
                {
                    return Cast::toInt($this->request->getQuery('bigint_col_from'));
                }
                public function getBigintColTo(): ?int
                {
                    return Cast::toInt($this->request->getQuery('bigint_col_to'));
                }
                public function getDecimalColFrom(): ?float
                {
                    return Cast::toFloat($this->request->getQuery('decimal_col_from'));
                }
                public function getDecimalColTo(): ?float
                {
                    return Cast::toFloat($this->request->getQuery('decimal_col_to'));
                }
                public function getFloatColFrom(): ?float
                {
                    return Cast::toFloat($this->request->getQuery('float_col_from'));
                }
                public function getFloatColTo(): ?float
                {
                    return Cast::toFloat($this->request->getQuery('float_col_to'));
                }
                public function getDoubleColFrom(): ?float
                {
                    return Cast::toFloat($this->request->getQuery('double_col_from'));
                }
                public function getDoubleColTo(): ?float
                {
                    return Cast::toFloat($this->request->getQuery('double_col_to'));
                }
                public function getDateColFrom(): ?\DateTimeInterface
                {
                    return Cast::toDate($this->request->getQuery('date_col_from'));
                }
                public function getDateColTo(): ?\DateTimeInterface
                {
                    return Cast::toDate($this->request->getQuery('date_col_to'));
                }
                public function getTimeColFrom(): ?\DateTimeInterface
                {
                    return Cast::toTime($this->request->getQuery('time_col_from'));
                }
                public function getTimeColTo(): ?\DateTimeInterface
                {
                    return Cast::toTime($this->request->getQuery('time_col_to'));
                }
                public function getDatetimeColFrom(): ?\DateTimeInterface
                {
                    return Cast::toDatetime($this->request->getQuery('datetime_col_from'));
                }
                public function getDatetimeColTo(): ?\DateTimeInterface
                {
                    return Cast::toDatetime($this->request->getQuery('datetime_col_to'));
                }
                public function getKeyword(): ?string
                {
                    return Cast::toString($this->request->getQuery('keyword'));
                }
            }
        ))->getQuery();
    }

    /**
     * 
     * @return array
     */
    private function getSearchSettings() : array
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