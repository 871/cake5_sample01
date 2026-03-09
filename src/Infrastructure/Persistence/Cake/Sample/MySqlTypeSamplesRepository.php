<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Cake\Sample;

use App\Domain\Sample\MySqlTypeSamples\Entity\MySqlTypeSample as DomainEntity;
use App\Domain\Sample\MySqlTypeSamples\Repository\MySqlTypeSamplesRepository as DomainMySqlTypeSamplesRepository;
use App\Domain\Sample\MySqlTypeSamples\SearchCondition;
use Cake\ORM\Query;

class MySqlTypeSamplesRepository implements DomainMySqlTypeSamplesRepository
{
    /**
     * @param \App\Domain\Sample\MySqlTypeSamples\SearchCondition $condition
     * @return \Cake\ORM\Query
     */
    public function search(SearchCondition $condition): Query
    {
        return (new MySqlTypeSamplesRepository\Search($condition))->run();
    }

    /**
     * 新規作成
     *
     * @param \App\Domain\Sample\MySqlTypeSamples\Entity\MySqlTypeSample $domainEntity
     * @return \App\Domain\Sample\MySqlTypeSamples\Entity\MySqlTypeSample
     */
    public function create(DomainEntity $domainEntity): DomainEntity
    {
        return (new MySqlTypeSamplesRepository\Create($domainEntity))->run();
    }
}
