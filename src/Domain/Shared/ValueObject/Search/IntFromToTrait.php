<?php
declare(strict_types=1);

namespace App\Domain\Shared\ValueObject\Search;

trait IntFromToTrait
{
    /**
     * @return ?int
     */
    public function toIntFrom(): ?int
    {
        return $this->fromValue;
    }

    /**
     * @return ?int
     */
    public function toIntTo(): ?int
    {
        return $this->toValue;
    }

    /**
     * @return string
     */
    public function toStringFrom(): string
    {
        return (string)$this->fromValue;
    }
    /**
     * 
     * @return string
     */
    public function toStringTo(): string
    {
        return (string)$this->toValue;
    }

    /**
     * @return array<int, null|int>
     */
    public function toArray(): array
    {
        return [
            $this->fromValue,
            $this->toValue,
        ];
    }
}
