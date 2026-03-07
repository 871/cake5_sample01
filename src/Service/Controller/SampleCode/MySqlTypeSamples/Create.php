<?php
declare(strict_types=1);

namespace App\Service\Controller\SampleCode\MySqlTypeSamples;

use App\Service\Controller\Shared\Process\Process\Fields\ProcessParams;
use App\Service\Controller\Shared\Process\Process\InputProcess;
use App\Service\Controller\Shared\Process\ProcessFactory;
use App\Service\Controller\Shared\Process\ProcessProvider;
use App\Service\Controller\Shared\Process\ProcessRepository;
use App\Service\Controller\Shared\Process\Process\Fields\ProcessId;
use App\Service\Controller\Shared\ServiceInterface;
use App\Service\Controller\Shared\ServiceTrait;

final class Create implements ServiceInterface
{
    use ServiceTrait;

    /**
     * @param array<string> $ignoreActions
     * @return bool
     */
    public function existsInputProcess(array $ignoreActions = []): bool
    {
        if (in_array($this->request->getAction(), $ignoreActions, true)) {

            return true;
        }

        /** @var \App\Service\Controller\Shared\Process\ProcessProvider $processProvider */
        $processProvider = $this->createService(ProcessProvider::class);
        $inputProcess = $processProvider->provide(
            processClassName: InputProcess::class, 
            serviceClassName: self::class, 
            processId: new ProcessId($this->request->getParam('process_id')),
        );

        return $inputProcess !== null;
    }

    /**
     * @return \App\Service\Controller\Shared\Process\Process\InputProcess;
     */
    public function startInputProcess(): InputProcess
    {
        /** @var \App\Service\Controller\Shared\Process\ProcessFactory $processFactory */
        $processFactory = $this->createService(ProcessFactory::class);
        /** @var \App\Service\Controller\Shared\Process\Process\InputProcess $process */
        $process = $processFactory->start(
            processClassName: InputProcess::class, 
            serviceClassName: self::class, 
            processParams: new ProcessParams([
                'xxx' => 'xxx',
            ])
        );

        return $process;
    }

    /**
     * @return \App\Service\Controller\Shared\Process\Process\InputProcess;
     */
    public function getInputProcess(): InputProcess
    {   
        /** @var \App\Service\Controller\Shared\Process\ProcessProvider $processProvider */
        $processProvider = $this->createService(ProcessProvider::class);
        $inputProcess = $processProvider->provide(
            processClassName: InputProcess::class, 
            serviceClassName: self::class, 
            processId: new ProcessId($this->request->getParam('process_id')),
        );

        return $inputProcess;
    }
}
