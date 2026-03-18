<?php
declare(strict_types=1);

namespace App\Domain\Shared\ValueObject\Trait;

trait FloatTrait
{
    /**
     * @return float
     */
    public function toFloat(): float
    {
        return (float)$this->value;
    }

    /**
     * @return ?float
     */
    public function toFloatOrNull(): ?float
    {
        return $this->value === null || $this->value === '' ? null : (float)$this->value;
    }

    /**
     * @return string
     */
    public function toString(): string
    {
        return (string)$this->value;
    }

    /**
     * @return ?string
     */
    public function toStringOrNull(): ?string
    {
        return $this->value === null || $this->value === '' ? null : (string)$this->value;
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
     * @return self
     */
    public static function fromString(?string $value): self
    {
        return new self(
            $value === null || $value === '' ? null : $value,
        );
    }
}
