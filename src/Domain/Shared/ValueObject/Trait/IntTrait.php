<?php
declare(strict_types=1);

namespace App\Domain\Shared\ValueObject\Trait;

use DomainException;

trait IntTrait
{
    /**
     * @return int
     */
    public function toInt(): ?int
    {
        return $this->value === null ? null : (int)$this->value;
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

    /**
     * @param ?string $value
     * @return self;
     */
    public static function fromString(?string $value): self
    {
        if ($value === null || $value === '') {
            return new self(null);
        }

        if (preg_match('/^-?\d+$/', $value)) {
            return new self($value);
        }

        throw new DomainException(
            self::class . ' value not int Error'
                . '[value: ' . $value . ']',
        );
    }
}
