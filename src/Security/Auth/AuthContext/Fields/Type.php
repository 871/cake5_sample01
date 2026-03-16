<?php
declare(strict_types=1);

namespace App\Security\Auth\AuthContext\Fields;

use App\Security\Auth\AuthContext;
use DomainException;
use Stringable;

class Type implements Stringable
{
    /**
     * @param string $value
     */
    public function __construct(
        private readonly string $value,
    ) {
        if (
            in_array($this->value, [
            AuthContext::TYPE_ANONYMOUS,
            AuthContext::TYPE_CUSTMER,
            AuthContext::TYPE_USER,
            AuthContext::TYPE_ADMIN,
            ], true) === false
        ) {
            throw new DomainException(
                self::class . ' Generate Error'
                . '[type: ' . $this->value . ']',
            );
        }
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
