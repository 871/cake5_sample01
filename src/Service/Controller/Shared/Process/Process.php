<?php
declare(strict_types=1);

namespace App\Service\Controller\Shared\Process;

interface Process
{
    public const PREFIX = 'process';

    /**
     * @param \App\Service\Controller\Shared\Process\Process\Fields\ProcessId $processId
     * @param \App\Service\Controller\Shared\Process\Process\Fields\ProcessParams $processParams
     */
    public function __construct(
        Process\Fields\ProcessId $processId,
        Process\Fields\ProcessParams $processParams,
    );

    /**
     * @param \App\Service\Controller\Shared\Process\Process\Fields\ProcessParams $processParams
     * @return self
     */
    public function setProcessParams(Process\Fields\ProcessParams $processParams): self;

    /**
     * @return \App\Service\Controller\Shared\Process\Process\Fields\ProcessId
     */
    public function getId(): Process\Fields\ProcessId;

    /**
     * @return Process\Fields\ProcessParams;
     */
    public function getProcessParams(): Process\Fields\ProcessParams;
}
