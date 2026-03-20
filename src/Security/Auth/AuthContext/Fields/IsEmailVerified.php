<?php
declare(strict_types=1);

namespace App\Security\Auth\AuthContext\Fields;

use DomainException;
use Stringable;

class IsEmailVerified implements Stringable
{
    /**
     * @param string $value
     */
    public function __construct(
        private readonly string $value,
    ) {
        if (
            in_array($this->value, ['0', '1'], true) === false
        ) {
            throw new DomainException(
                self::class . ' Generate Error'
                . '[type: ' . $this->value . ']',
            );
        }
    }

    /**
     * @return bool
     */
    public function toBool(): bool
    {
        return $this->value === '1';
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
