<?php
declare(strict_types=1);

namespace App\Domain\Sample\MySqlTypeSamples\ValueObject;

use App\Domain\Shared\ValueObject\Trait\IntTrait;
use DomainException;

class BigintCol
{
    use IntTrait;

    public const MIN = -100000000000;
    public const MAX = 100000000000;

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
