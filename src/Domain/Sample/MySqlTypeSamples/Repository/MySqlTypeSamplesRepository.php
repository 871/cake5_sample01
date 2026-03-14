<?php
declare(strict_types=1);

namespace App\Domain\Sample\MySqlTypeSamples\Repository;

use App\Domain\Sample\MySqlTypeSamples\Entity\MySqlTypeSample;
use App\Domain\Sample\MySqlTypeSamples\SearchCondition;
use App\Domain\Sample\MySqlTypeSamples\ValueObject as Vo;
use Cake\ORM\Query;

interface MySqlTypeSamplesRepository
{
    /**
     * Memo: Cake5のController::paginate()の仕様を優先した設計とするため、Cake\ORM\Queryを直接返す形にしています。 --- IGNORE ---
     * 完全なDDDへ再設計する場合は、ドメインサービス内でページネーションやソートの処理も完結させる形にすることも検討してください。 --- IGNORE ---
     *
     * @return \Cake\ORM\Query
     */
    public function search(SearchCondition $condition): Query;

    /**
     * 作成
     *
     * @param \App\Domain\Sample\MySqlTypeSamples\Entity\MySqlTypeSample $entity
     * @return \App\Domain\Sample\MySqlTypeSamples\Entity\MySqlTypeSample
     */
    public function create(MySqlTypeSample $entity): MySqlTypeSample;

    /**
     * 取得
     *
     * @param \App\Domain\Sample\MySqlTypeSamples\ValueObject\Id $id
     * @return \App\Domain\Sample\MySqlTypeSamples\Entity\MySqlTypeSample
     */
    public function read(Vo\Id $id): MySqlTypeSample;

    /**
     * 更新
     *
     * @param \App\Domain\Sample\MySqlTypeSamples\Entity\MySqlTypeSample $entity
     * @return \App\Domain\Sample\MySqlTypeSamples\Entity\MySqlTypeSample
     */
    public function update(MySqlTypeSample $entity): MySqlTypeSample;

    /**
     * 削除
     *
     * @param \App\Domain\Sample\MySqlTypeSamples\ValueObject\Id $id
     * @return \App\Domain\Sample\MySqlTypeSamples\Entity\MySqlTypeSample
     */
    public function delete(Vo\Id $id): MySqlTypeSample;
}
