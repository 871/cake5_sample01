<?php
declare(strict_types=1);

namespace App\Service\Controller\Shared\Process;

use App\Service\Controller\Shared\ServiceInterface;
use App\Service\Controller\Shared\ServiceTrait;
use InvalidArgumentException;

final class ProcessRepository implements ServiceInterface
{
    use ServiceTrait;

    /**
     * @param string $serviceClassName
     * @param \App\Service\Controller\Shared\Process\Process $process
     * @return void
     */
    public function save(string $serviceClassName, Process $process): void
    {
        if (!is_subclass_of($serviceClassName, ServiceInterface::class)) {
            throw new InvalidArgumentException(
                'Service class must implement ' . ServiceInterface::class
                . '[serviceClassName: ' . $serviceClassName . ']',
            );
        }

        $sessionKey = new SessionKey(
            prefix: Process::PREFIX,
            type: $this->authContext->getType(),
            accountId: $this->authContext->getAccountId(),
            serviceClassName: $serviceClassName,
            processId: $process->getProcessParams()->getId()->toString(),
        );

        $this->request->getSession()->check((string)$sessionKey)
            ? $this->request->getSession()->write((string)$sessionKey, $process->getProcessParams()->toArray())
            : throw new InvalidArgumentException(
                'An invalid Process Instance was set'
                . '[ProcessId: ' . $process->getProcessParams()->getId()->toString() . ']'
                . '[ProcessParams: ' . print_r($process->getProcessParams()->toArray(), true) . ']',
            );
    }
}
