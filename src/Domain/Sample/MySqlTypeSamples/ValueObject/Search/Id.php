<?php
declare(strict_types=1);

namespace App\Domain\Sample\MySqlTypeSamples\ValueObject\Search;

use DomainException;
use App\Domain\Shared\ValueObject\Search\StringTrait;

class Id
{
    use StringTrait;

    public const LENGTH = 36; // UUIDの上限文字数

    /**
     * @param ?string $value
     */
    public function __construct(
        private readonly ?string $value,
    ) {
        $value === null || strlen($value) <= self::LENGTH
        || throw new DomainException(
            self::class . ' value length Error'
                . '[maxlength: ' . (string)self::LENGTH . ']'
                . '[process_id: ' . $value . ']',
        );
    }
}
