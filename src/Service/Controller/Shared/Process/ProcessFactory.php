<?php
declare(strict_types=1);

namespace App\Service\Controller\Shared\Process;

use App\Service\Controller\Shared\Process\Process\Fields\ProcessParams;
use App\Service\Controller\Shared\ServiceInterface;
use App\Service\Controller\Shared\ServiceTrait;
use DomainException;

final class ProcessFactory implements ServiceInterface
{
    use ServiceTrait;

    /**
     * ProcessInstanceの内容（ProcessParams）をSessionに保存してからProcessInstanceを作成する
     *
     * @param string $processClassName
     * @param \App\Service\Controller\Shared\Process\Process\Fields\ProcessParams $processParams
     * @return \App\Service\Controller\Shared\Process\ProcessInterface
     */
    public function start(
        string $processClassName,
        ProcessParams $processParams,
    ): ProcessInterface {
        if (!is_subclass_of($processClassName, ProcessInterface::class)) {
            throw new DomainException(
                'Process class must implement ' . ProcessInterface::class
                . '[processClassName: ' . $processClassName . ']',
            );
        }

        return new $processClassName(
            processId: $this->storeAndGenerateId($processParams),
            processParams: $processParams,
        );
    }

    /**
     * @param \App\Service\Controller\Shared\Process\Process\Fields\ProcessParams $processParams
     * @return \App\Service\Controller\Shared\Process\Process\Fields\ProcessId
     */
    private function storeAndGenerateId(
        ProcessParams $processParams,
    ): Process\Fields\ProcessId {
        $processId = new Process\Fields\ProcessId(uniqid());
        $sessionKey = new SessionKey(
            prefix: ProcessInterface::PREFIX,
            type: $this->authContext->getType(),
            accountId: $this->authContext->getAccountId(),
            processId: $processId,
        );

        return $this->request->getSession()->check((string)$sessionKey)
            ? $this->storeAndGenerateId($processParams)
            : (function () use ($processId, $sessionKey, $processParams): Process\Fields\ProcessId {

                $this->request->getSession()->write((string)$sessionKey, $processParams->toArray());

                return $processId;
            })();
    }
}
