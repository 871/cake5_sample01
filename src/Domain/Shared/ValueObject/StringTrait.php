<?php
declare(strict_types=1);

namespace App\Domain\Shared\ValueObject;

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
}
