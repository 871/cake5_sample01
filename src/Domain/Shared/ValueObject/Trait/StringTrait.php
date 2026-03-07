<?php
declare(strict_types=1);

namespace App\Domain\Shared\ValueObject\Trait;

trait StringTrait
{
    /**
     * @return string
     */
    public function toString(): string
    {
        return $this->value ?? '';
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
