<?php
declare(strict_types=1);

namespace App\Service\Controller\Shared\Process;

use App\Service\Controller\Shared\Process\Process\Fields\ProcessId;
use App\Service\Controller\Shared\Process\Process\Fields\ProcessParams;
use App\Service\Controller\Shared\ServiceInterface;
use App\Service\Controller\Shared\ServiceTrait;
use InvalidArgumentException;

final class ProcessProvider implements ServiceInterface
{
    use ServiceTrait;

    /**
     * @param string $processClassName
     * @param string $serviceClassName
     * @param \App\Service\Controller\Shared\Process\Process\Fields\ProcessId $processId
     * @return ?\App\Service\Controller\Shared\Process\Process
     */
    public function provide(string $processClassName, string $serviceClassName, ProcessId $processId): ?Process
    {
        if (!is_subclass_of($processClassName, Process::class)) {
            throw new InvalidArgumentException(
                'Process class must implement ' . Process::class
                . '[processClassName: ' . $processClassName . ']',
            );
        }

        if (!is_subclass_of($serviceClassName, ServiceInterface::class)) {
            throw new InvalidArgumentException(
                'Service class must implement ' . ServiceInterface::class
                . '[serviceClassName: ' . $serviceClassName . ']',
            );
        }

        $processParams = $this->getProcessParams($serviceClassName, $processId);

        return $processParams === null ? null : new $processClassName(
            processId: $processId,
            processParams: $processParams,
        );
    }

    /**
     * @param string $serviceClassName
     * @param \App\Service\Controller\Shared\Process\Process\Fields\ProcessId $processId
     * @return ?\App\Service\Controller\Shared\Process\Process\Fields\ProcessParams
     */
    private function getProcessParams(string $serviceClassName, ProcessId $processId): ?ProcessParams
    {
        $processId = new Process\Fields\ProcessId(uniqid());
        $sessionKey = new SessionKey(
            prefix: Process::PREFIX,
            type: $this->authContext->getType(),
            accountId: $this->authContext->getAccountId(),
            serviceClassName: $serviceClassName,
            processId: $processId,
        );

        return $this->request->getSession()->check((string) $sessionKey)
            ? new ProcessParams((array) $this->request->getSession()->read((string) $sessionKey))
            : null;
    }
}
