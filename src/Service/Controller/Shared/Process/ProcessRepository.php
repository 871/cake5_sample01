<?php
declare(strict_types=1);

namespace App\Service\Controller\Shared\Process;

use App\Service\Controller\Shared\ServiceInterface;
use App\Service\Controller\Shared\ServiceTrait;
use DomainException;

final class ProcessRepository implements ServiceInterface
{
    use ServiceTrait;

    /**
     * Process Instance の内容をSessionに保存する
     *
     * @param string $serviceClassName
     * @param \App\Service\Controller\Shared\Process\ProcessInterface $process
     * @return void
     */
    public function save(string $serviceClassName, ProcessInterface $process): void
    {
        if (!is_subclass_of($serviceClassName, ServiceInterface::class)) {
            throw new DomainException(
                'Service class must implement ' . ServiceInterface::class
                . '[serviceClassName: ' . $serviceClassName . ']',
            );
        }

        $sessionKey = new SessionKey(
            prefix: ProcessInterface::PREFIX,
            type: $this->authContext->getType(),
            accountId: $this->authContext->getAccountId(),
            serviceClassName: $serviceClassName,
            processId: $process->getId(),
        );

        $this->request->getSession()->check((string)$sessionKey)
            ? $this->request->getSession()->write((string)$sessionKey, $process->getProcessParams()->toArray())
            : throw new DomainException(
                'An invalid Process Instance was set'
                . '[ProcessId: ' . $process->getId()->toString() . ']'
                . '[ProcessParams: ' . print_r($process->getProcessParams()->toArray(), true) . ']',
            );
    }
}
