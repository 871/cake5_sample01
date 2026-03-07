<?php
declare(strict_types=1);

namespace App\Domain\Sample\MySqlTypeSamples\Repository;

use App\Domain\Sample\MySqlTypeSamples\SearchCondition;
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
}
