<?php
declare(strict_types=1);

namespace App\Domain\Log\PageAccessLogs\ValueObject;

use App\Domain\Shared\ValueObject\Trait\IntTrait;
use DomainException;
use Stringable;

class AccountId implements Stringable
{
    use IntTrait;

    public const MIN = 1;

    private int $value;

    /**
     * @param string $value
     */
    public function __construct(string $value)
    {
        if ($value === '') {
            throw new DomainException(
                self::class . ' value format Error'
                . '[value: ' . $value . ']',
            );
        }

        if (!preg_match('/^\d+$/', $value) || (int)$value < self::MIN) {
            throw new DomainException(
                self::class . ' value range Error'
                . '[value: ' . $value . ']',
            );
        }

        $this->value = (int)$value;
    }

    /**
     * @param ?string $value
     * @return self
     */
    public static function fromString(?string $value): self
    {
        if ($value === null || $value === '') {
            throw new DomainException(
                self::class . ' value is required',
            );
        }

        return new self($value);
    }

    /**
     * @return ?int
     */
    public function toIntOrNull(): ?int
    {
        return $this->value;
    }

    /**
     * @return ?string
     */
    public function toStringOrNull(): ?string
    {
        return (string)$this->value;
    }
}
