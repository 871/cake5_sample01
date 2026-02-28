<?php
declare(strict_types=1);

namespace App\Service\Controller\Shared\Process;

/**
 *
 */
final class ProcessFactory implements \App\Service\Controller\Shared\ServiceInterface
{
    use \App\Service\Controller\Shared\ServiceTrait;

    /**
     * 
     * 
     */
    public function start(string $processClassName, string $serviceClassName, array $processParams) : Process
    {
        if (!is_subclass_of($processClassName, Process::class)) {

            throw new \InvalidArgumentException(
                'Process class must implement ' . Process::class
                . '[processClassName: ' . $processClassName . ']'
            );
        }

        return new $processClassName(
            processId : $this->storeAndGenerateId($serviceClassName, $processParams),
            processParams : $processParams
        );
    }
    
    /**
     * 
     * @param string $serviceClassName
     * @param array $processParams
     * @return Process\Fields\ProcessId
     */
    private function storeAndGenerateId(string $serviceClassName, array $processParams) : Process\Fields\ProcessId
    {
        $processId = new Process\Fields\ProcessId(uniqid());
        $sessionKey = new SessionKey(
            prefix : Process::PREFIX,
            type : $this->authContext->getType(),
            accountId : $this->authContext->getAccountId(),
            serviceClassName : $serviceClassName,
            processId : $processId,
        );

        return $this->request->getSession()->check((string) $sessionKey)
            ? $this->storeAndGenerateId($serviceClassName, $processParams)
            : (function() use ($processId, $sessionKey, $processParams) : Process\Fields\ProcessId {

                $this->request->getSession()->write((string) $sessionKey, $processParams);

                return $processId;
            })();
    }
    
}