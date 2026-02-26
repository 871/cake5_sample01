<?php
declare(strict_types=1);

namespace App\Domain\Sample\Sample\MySqlTypeSamples;

/**
 *
 */
interface Search
{
    public function __construct(SearchConditionInterface $condition);

    /**
     * 
     * @return \Cake\ORM\Query
     */
    public function getQuery() : \Cake\ORM\Query;
}