<?php
declare(strict_types=1);

namespace App\Model\Table;


use Cake\ORM\Locator\TableLocator;

trait TableLocatorTrait
{
    public static function getInstance() : self 
    {
        return (new TableLocator())->get(self::class);
    }
}