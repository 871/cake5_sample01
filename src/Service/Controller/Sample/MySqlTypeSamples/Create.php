<?php
declare(strict_types=1);

namespace App\Service\Controller\Sample\MySqlTypeSamples;

use \App\Security\Input\Cast;
use \App\Domain\Sample\MySqlTypeSamples\SearchCondition;
use \App\Infrastructure\Persistence\Cake\Sample\MySqlTypeSamplesRepository;


/**
 *
 */
final class Create implements \App\Service\Controller\Shared\ServiceInterface
{
    use \App\Service\Controller\Shared\ServiceTrait;

    /**
     * 
     * @param array $ignoreActions
     * @return bool
     */
    public function existsInputProcess(array $ignoreActions = []) : bool
    {
        // TODO

        return true;
    }


    public function startInputProcess() : InputProcess
    {
        

        return new InputProcess();
    }

}