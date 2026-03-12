<?php
declare(strict_types=1);

namespace App\Service\Controller\Shared\Process\Process;

use App\Service\Controller\Shared\Process\ProcessInterface;
use Cake\Utility\Hash;

final class InputProcess implements ProcessInterface
{
    /**
     * @param \App\Service\Controller\Shared\Process\Process\Fields\ProcessId $processId
     * @param \App\Service\Controller\Shared\Process\Process\Fields\ProcessParams $processParams
     */
    public function __construct(
        private readonly Fields\ProcessId $processId,
        private readonly Fields\ProcessParams $processParams,
    ) {
        // 処理なし
    }

    /**
     * @param \App\Service\Controller\Shared\Process\Process\Fields\ProcessParams $processParams
     * @return self
     */
    public function setProcessParams(Fields\ProcessParams $processParams): self
    {
        return new self($this->processId, $processParams);
    }

    /**
     * @return \App\Service\Controller\Shared\Process\Process\Fields\ProcessId
     */
    public function getId(): Fields\ProcessId
    {
        return $this->processId;
    }

    /**
     * @return Fields\ProcessParams;
     */
    public function getProcessParams(): Fields\ProcessParams
    {
        return $this->processParams;
    }

    /**
     * @return array
     */
    public function getInputs(): array
    {
        return Hash::expand(
            array_map(
                fn($v) => (string)$v, 
                Hash::flatten($this->processParams->toArray()),
            ),
        );
    }

    /**
     * @param string $path
     * @param ?string $default
     * @return null|string|array
     */
    public function getInput(string $path, ?string $default = null): null|string|array
    {
        $input = Hash::get($this->processParams->toArray(), $path, $default);
        if ($input === null) {

            return null;
        }

        if (is_array($input)) {

            return Hash::expand(
                array_map(
                    fn($v) => (string)$v, 
                    Hash::flatten($input),
                ),
            );
        }

        return (string)$input;
    }
}
