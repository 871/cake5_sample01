<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Locator\TableLocator;

/**
 * Memo: テーブルクラスから直感的にインスタンスを取得するためのTraitです。
 * 例えば、MySqlTypeSamplesTableクラスでこのTraitを使用することで、
 * MySqlTypeSamplesTable::getInstance() でインスタンスを取得できるようになります。
 */
trait TableLocatorTrait
{
    public static function getInstance(): self
    {
        return (new TableLocator())->get(self::class);
    }
}
