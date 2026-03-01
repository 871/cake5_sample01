<?php
declare(strict_types=1);

namespace App\Security\Auth\AuthContext\Fields;

use App\Security\Auth\AuthContext;
use Exception;
use Stringable;

class Type implements Stringable
{
    public function __construct(
        private readonly string $value,
    ) {
        in_array($this->value, [
            AuthContext::TYPE_ANONYMOUS,
        ], true) || throw new Exception(
            self::class . ' Generate Error'
            . '[type: ' . $this->value . ']',
        );
    }

    /**
     * @return string
     */
    public function toString(): string
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->toString();
    }
}
