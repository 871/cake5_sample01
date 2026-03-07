<?php
declare(strict_types=1);

namespace App\Domain\Shared\ValueObject\Trait;

trait JsonTrait
{
    /**
     * @return string
     */
    public function toString(): string
    {
        if ($this->value === null) {
            return '';
        }

        return json_encode($this->value) ? : '';
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->toString();
    }

    /**
     * @return array<mixed>
     */
    public function jsonSerialize(): array
    {
        if ($this->value === null || $this->value === []) {
            return [];
        }

        return (array)$this->value;
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
