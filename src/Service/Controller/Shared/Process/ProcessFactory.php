<?php
declare(strict_types=1);

namespace App\Service\Controller\Shared\Process;

use App\Service\Controller\Shared\Process\Process\Fields\ProcessParams;
use App\Service\Controller\Shared\ServiceInterface;
use App\Service\Controller\Shared\ServiceTrait;
use InvalidArgumentException;

final class ProcessFactory implements ServiceInterface
{
    use ServiceTrait;

    /**
     * @param string $processClassName
     * @param string $serviceClassName
     * @param \App\Service\Controller\Shared\Process\Process\Fields\ProcessParams $processParams
     * @return \App\Service\Controller\Shared\Process\Process
     */
    public function start(string $processClassName, string $serviceClassName, ProcessParams $processParams): Process
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

        return new $processClassName(
            processId: $this->storeAndGenerateId($serviceClassName, $processParams),
            processParams: $processParams,
        );
    }

    /**
     * @param string $serviceClassName
     * @param \App\Service\Controller\Shared\Process\Process\Fields\ProcessParams $processParams
     * @return \App\Service\Controller\Shared\Process\Process\Fields\ProcessId
     */
    private function storeAndGenerateId(
        string $serviceClassName,
        ProcessParams $processParams,
    ): Process\Fields\ProcessId {
        $processId = new Process\Fields\ProcessId(uniqid());
        $sessionKey = new SessionKey(
            prefix: Process::PREFIX,
            type: $this->authContext->getType(),
            accountId: $this->authContext->getAccountId(),
            serviceClassName: $serviceClassName,
            processId: $processId,
        );

        return $this->request->getSession()->check((string)$sessionKey)
            ? $this->storeAndGenerateId($serviceClassName, $processParams)
            : (function () use ($processId, $sessionKey, $processParams): Process\Fields\ProcessId {

                $this->request->getSession()->write((string)$sessionKey, $processParams->toArray());

                return $processId;
            })();
    }
}
