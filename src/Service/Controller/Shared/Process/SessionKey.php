<?php
declare(strict_types=1);

namespace App\Service\Controller\Shared\Process;

use \App\Security\Auth\AuthContext\Fields;

/**
 *
 */
final class SessionKey implements \Stringable
{
    /**
     * 
     * @param string $prefix
     * @param Fields\Type $type
     * @param Fields\AccountId $accountId
     * @param string $serviceClassName
     * @param Process\Fields\ProcessId $processId
     */
    public function __construct(
        private readonly string $prefix,
        private readonly Fields\Type $type,
        private readonly Fields\AccountId $accountId,
        private readonly string $serviceClassName,
        private readonly Process\Fields\ProcessId $processId,
    ) {
        //　処理なし
    }

    /**
     * 
     * @return string
     */
    public function toString() : string
    {
        return join('.', array_filter([
            $this->prefix,
            $this->type->toString(),
            $this->accountId->toInt(),
            $this->serviceClassName,
            $this->processId->toString(),
        ], fn($v) => $v !== null));
    }

    /**
     * 
     * @return string
     */
    public function __toString() : string
    {
        return $this->toString();
    }
}