<?php
declare(strict_types=1);

namespace App\Security\Auth\AuthContext\Fields;

use DomainException;
use Stringable;

class AccountId implements Stringable
{
    const int MAX = 2147483647; // MySql SIGNED INT の上限値
    /**
     * @param ?int $value
     */
    public function __construct(
        private readonly ?int $value,
    ) {
        $this->value === null
        || ($this->value > 0 && $this->value <= self::MAX)
        || throw new DomainException(
            self::class . ' Generate Error'
            . '[value: ' . (string)$this->value . ']',
        );
    }

    /**
     * @return int
     */
    public function toInt(): ?int
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function toString(): string
    {
        return (string)$this->value;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->toString();
    }
}
