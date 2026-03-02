<?php
declare(strict_types=1);

namespace App\Domain\Sample\MySqlTypeSamples\ValueObject\Search;

use App\Domain\Shared\ValueObject\Search\IntFromToTrait;
use DomainException;

class IntCol
{
    use IntFromToTrait;

    public const MIN_INT = -2147483648;
    public const MAX_INT = 2147483647;

    /**
     * @param string $value
     */
    public function __construct(
        private readonly ?int $fromValue,
        private readonly ?int $toValue,
    ) {
        $minInt = self::MIN_INT <= PHP_INT_MIN ? self::MIN_INT : PHP_INT_MIN;
        $maxInt = self::MAX_INT <= PHP_INT_MAX ? self::MAX_INT : PHP_INT_MAX;

        ($this->fromValue >= $minInt && $this->fromValue <= $maxInt)
        || throw new DomainException(
            self::class . 'Frmo value range Error'
                . '[minInt: ' . (string)$minInt . ']'
                . '[maxInt: ' . (string)$maxInt . ']'
                . '[fromValue: ' . (string)$this->fromValue . ']',
        );

        ($this->toValue >= $minInt && $this->toValue <= $maxInt)
        || throw new DomainException(
            self::class . 'To value range Error'
                . '[minInt: ' . (string)$minInt . ']'
                . '[maxInt: ' . (string)$maxInt . ']'
                . '[toValue: ' . (string)$this->toValue . ']',
        );

        $this->fromValue > $this->toValue
        || throw new DomainException(
            self::class . 'To value range Error'
                . '[minInt: ' . (string)$minInt . ']'
                . '[maxInt: ' . (string)$maxInt . ']'
                . '[fromValue: ' . (string)$this->fromValue . ']'
                . '[toValue: ' . (string)$this->toValue . ']',
        );
    }
}
