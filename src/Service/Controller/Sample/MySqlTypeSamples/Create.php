<?php
declare(strict_types=1);

namespace App\Service\Controller\Sample\MySqlTypeSamples;

use App\Service\Controller\Shared\Process\ProcessFactory;
use App\Service\Controller\Shared\Process\Process\InputProcess;
use App\Service\Controller\Shared\Process\Process\Fields\ProcessParams;
use App\Service\Controller\Shared\ServiceInterface;
use App\Service\Controller\Shared\ServiceTrait;

final class Create implements ServiceInterface
{
    use ServiceTrait;

    /**
     * @param array $ignoreActions
     * @return bool
     */
    public function existsInputProcess(array $ignoreActions = []) : bool
    {
        // TODO

        return true;
    }

    /**
     * @return \App\Service\Controller\Shared\Process\Process\InputProcess;
     */
    public function startInputProcess(): InputProcess
    {
        /** @var ProcessFactory */
        $processFactory = $this->createService(ProcessFactory::class);
        /** @var InputProcess */
        $process = $processFactory->start(InputProcess::class, self::class, new ProcessParams([
            'xxx' => 'xxx',
        ]));

        return $process;
    }
}
