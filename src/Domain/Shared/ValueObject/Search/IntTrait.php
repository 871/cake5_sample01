<?php
declare(strict_types=1);

namespace App\Domain\Shared\ValueObject\Search;

trait IntTrait
{
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
