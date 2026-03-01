<?php
declare(strict_types=1);

namespace App\Service\Controller\Shared\Process;

/**
 *
 */
interface Process
{
    const PREFIX = 'process';

    public function __construct(
        Process\Fields\ProcessId $processId,
        Process\Fields\ProcessParams $processParams
    );

    /**
     * 
     * @param Process\Fields\ProcessParams $processParams
     * @return self
     */
    public function setProcessParams(Process\Fields\ProcessParams $processParams);

    /**
     * 
     * @return Process\Fields\ProcessId
     */
    public function getId() : Process\Fields\ProcessId;

    /**
     * 
     * @return Process\Fields\ProcessParams;
     */
    public function getProcessParams() : Process\Fields\ProcessParams;
}