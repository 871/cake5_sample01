<?php
declare(strict_types=1);

namespace App\Service\Controller\Shared\Process;

use App\Service\Controller\Shared\ServiceInterface;
use App\Service\Controller\Shared\ServiceTrait;
use DomainException;

final class ProcessDeleter implements ServiceInterface
{
    use ServiceTrait;

    /**
     * Sessionに保存されたProcess Instance の内容を削除する
     *
     * @param string $serviceClassName
     * @param \App\Service\Controller\Shared\Process\ProcessInterface $process
     * @return void
     */
    public function delete(string $serviceClassName, ProcessInterface $process): void
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
            ? $this->request->getSession()->delete((string)$sessionKey)
            : throw new DomainException(
                'An invalid Process Instance was set'
                . '[ProcessId: ' . $process->getId()->toString() . ']',
            );
    }
}
