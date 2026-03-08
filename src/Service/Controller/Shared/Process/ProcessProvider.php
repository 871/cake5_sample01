<?php
declare(strict_types=1);

namespace App\Service\Controller\Shared\Process;

use App\Service\Controller\Shared\Process\Process\Fields\ProcessId;
use App\Service\Controller\Shared\Process\Process\Fields\ProcessParams;
use App\Service\Controller\Shared\ServiceInterface;
use App\Service\Controller\Shared\ServiceTrait;
use DomainException;

final class ProcessProvider implements ServiceInterface
{
    use ServiceTrait;

    /**
     * Sessionに保存されたProcessInstanceの内容からProcessInstanceを取得する
     *
     * @param string $processClassName
     * @param string $serviceClassName
     * @param \App\Service\Controller\Shared\Process\Process\Fields\ProcessId $processId
     * @return ?\App\Service\Controller\Shared\Process\ProcessInterface
     */
    public function provide(string $processClassName, string $serviceClassName, ProcessId $processId): ?ProcessInterface
    {
        if (!is_subclass_of($processClassName, ProcessInterface::class)) {
            throw new DomainException(
                'Process class must implement ' . ProcessInterface::class
                . '[processClassName: ' . $processClassName . ']',
            );
        }

        if (!is_subclass_of($serviceClassName, ServiceInterface::class)) {
            throw new DomainException(
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
            prefix: ProcessInterface::PREFIX,
            type: $this->authContext->getType(),
            accountId: $this->authContext->getAccountId(),
            serviceClassName: $serviceClassName,
            processId: $processId,
        );

        return $this->request->getSession()->check((string)$sessionKey)
            ? new ProcessParams((array)$this->request->getSession()->read((string)$sessionKey))
            : null;
    }
}
