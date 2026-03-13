<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Cake\Sample;

use App\Domain\Sample\MySqlTypeSamples\Entity\MySqlTypeSample as DomainEntity;
use App\Domain\Sample\MySqlTypeSamples\Repository\MySqlTypeSamplesRepository as DomainMySqlTypeSamplesRepository;
use App\Domain\Sample\MySqlTypeSamples\SearchCondition;
use App\Domain\Sample\MySqlTypeSamples\ValueObject as Vo;
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

    /**
     * 取得
     *
     * @param \App\Domain\Sample\MySqlTypeSamples\ValueObject\Id $id
     * @return \App\Domain\Sample\MySqlTypeSamples\Entity\MySqlTypeSample
     */
    public function read(Vo\Id $id): DomainEntity
    {
        return (new MySqlTypeSamplesRepository\Read($id))->run();
    }

    /**
     * 更新
     *
     * @param \App\Domain\Sample\MySqlTypeSamples\Entity\MySqlTypeSample $domainEntity
     * @return \App\Domain\Sample\MySqlTypeSamples\Entity\MySqlTypeSample
     */
    public function update(DomainEntity $domainEntity): DomainEntity
    {
        return (new MySqlTypeSamplesRepository\Update($domainEntity))->run();
    }
}
