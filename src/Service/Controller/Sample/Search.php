<?php
declare(strict_types=1);

namespace App\Service\Controller\Sample;

/**
 *
 */
class Search implements \App\Service\Controller\ServiceInterface
{
    use \App\Service\Controller\ServiceTrait;

    /**
     * 
     * @var \App\Model\Table\Sample\MySqlTypeSamplesTable
     */
    private \App\Model\Table\Sample\MySqlTypeSamplesTable $mySqlTypeSamplesTable;

    private function __construct()
    {
        $this->mySqlTypeSamplesTable = \App\Model\Table\Sample\MySqlTypeSamplesTable::getInstance();
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
        return $this->mySqlTypeSamplesTable
            ->find();
    }
}