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
        array $processParams
    );

    /**
     * 
     * @param array $processParams
     * @return self
     */
    public function setProcessParams(array $processParams);

    /**
     * 
     * @return Process\Fields\ProcessId
     */
    public function getId() : Process\Fields\ProcessId;

    /**
     * 
     * @return array
     */
    public function getProcessParams() : array;
}