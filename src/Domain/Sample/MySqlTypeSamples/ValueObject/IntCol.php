<?php
declare(strict_types=1);

namespace App\Domain\Sample\MySqlTypeSamples\ValueObject;

use App\Domain\Shared\ValueObject\Trait\IntTrait;
use DomainException;
use Stringable;

class IntCol implements Stringable
{
    use IntTrait;

    public const STEP = 1;
    public const MIN = 1;
    public const MAX = 100;

    /**
     * @param ?int $value
     */
    public function __construct(
        private readonly ?int $value,
    ) {
        if (
            // Memo: STEPの判定は省略
            $this->value !== null
            && ($this->value < self::MIN || $this->value > self::MAX)
        ) {
            throw new DomainException(
                self::class . ' value range Error'
                . '[value: ' . (string)$this->value . ']'
                . '[MIN: ' . (string)self::MIN . ']'
                . '[MAX: ' . (string)self::MAX . ']'
                . '[STEP: ' . (string)self::STEP . ']',
            );
        }
    }
}
