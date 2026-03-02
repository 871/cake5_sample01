<?php
declare(strict_types=1);

namespace App\Domain\Sample\MySqlTypeSamples\ValueObject\Search;

use App\Domain\Shared\ValueObject\Search\IntFromToTrait;
use DomainException;

class BigintCol
{
    use IntFromToTrait;

    /**
     * @param ?int $fromValue
     * @param ?int $toValue
     */
    public function __construct(
        private readonly ?int $fromValue,
        private readonly ?int $toValue,
    ) {
        $this->fromValue === null || $this->toValue === null || $this->fromValue > $this->toValue
        || throw new DomainException(
            self::class . 'To value range Error'
                . '[fromValue: ' . (string)$this->fromValue . ']'
                . '[toValue: ' . (string)$this->toValue . ']',
        );
    }
}
