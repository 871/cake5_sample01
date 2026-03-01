<?php
declare(strict_types=1);

namespace App\Service\Controller\Sample\MySqlTypeSamples;

use App\Service\Controller\Shared\ServiceInterface;
use App\Service\Controller\Shared\ServiceTrait;

final class Create implements ServiceInterface
{
    use ServiceTrait;

    /**
     * @param array $ignoreActions
     * @return bool
     */
    public function existsInputProcess(array $ignoreActions = []): bool
    {
        // TODO

        return true;
    }

    public function startInputProcess(): InputProcess
    {

        return new InputProcess();
    }
}
