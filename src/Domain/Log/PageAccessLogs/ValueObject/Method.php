<?php
declare(strict_types=1);

namespace App\Domain\Log\PageAccessLogs\ValueObject;

use App\Domain\Shared\ValueObject\Trait\StringTrait;
use DomainException;
use Stringable;

class Method implements Stringable
{
    use StringTrait;

    public const MAX_LENGTH = 10;

    /**
     * @param string $value
     */
    public function __construct(
        private readonly string $value,
    ) {
        if (mb_strlen($value) > self::MAX_LENGTH) {
            throw new DomainException(
                self::class . ' value length Error'
                . '[value: ' . mb_strimwidth($value, 0, 200, '...') . ']',
            );
        }
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
     * @return ?string
     */
    public function toStringOrNull(): ?string
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function toString(): string
    {
        return $this->value;
    }
}
