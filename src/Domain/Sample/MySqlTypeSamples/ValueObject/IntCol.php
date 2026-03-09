<?php
declare(strict_types=1);

namespace App\Domain\Sample\MySqlTypeSamples\ValueObject;

use App\Domain\Shared\ValueObject\Trait\IntTrait;
use DomainException;
use Stringable;

class IntCol implements Stringable
{
    use IntTrait;

    public const MIN = 1;
    public const MAX = 100;

    /**
     * @param ?int $value
     */
    public function __construct(
        private readonly ?int $value,
    ) {
        if ($this->value !== null && ($this->value < self::MIN || $this->value > self::MAX)) {
            throw new DomainException(
                self::class . ' value range Error'
                . '[value: ' . (string)$this->value . ']'
                . '[MIN: ' . (string)self::MIN . ']'
                . '[MAX: ' . (string)self::MAX . ']',
            );
        }
    }
}
