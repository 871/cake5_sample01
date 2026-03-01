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
        private readonly Fields\ProcessParams $processParams
    ) {
        // 処理なし
    }

    /**
     * 
     * @param Fields\ProcessParams $processParams
     * @return self
     */
    public function setProcessParams(Fields\ProcessParams $processParams) : self
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
     * @return Fields\ProcessParams;
     */
    public function getProcessParams() : Fields\ProcessParams
    {
        return $this->processParams;
    }
}