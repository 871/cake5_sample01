<?php
declare(strict_types=1);

namespace App\Service\Controller\Shared\Process\Process;

use App\Service\Controller\Shared\Process\Process;

/**
 *
 */
final class InputProcess implements Process
{
    public function __construct(
        private readonly Fields\ProcessId $processId,
        private readonly array $processParams
    ) {
        // 処理なし
    }

    /**
     * 
     * @param array $processParams
     * @return self
     */
    public function setProcessParams(array $processParams) : self
    {
        return new self($this->processId, $processParams);
    }

    /**
     * 
     * @return Fields\ProcessId
     */
    public function getId() : Fields\ProcessId
    {
        return $this->processId;
    }

    /**
     * 
     * @return array
     */
    public function getProcessParams() : array
    {
        return $this->processParams;
    }
}