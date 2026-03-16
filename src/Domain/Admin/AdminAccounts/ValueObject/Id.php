<?php
declare(strict_types=1);

namespace App\Domain\Admin\AdminAccounts\ValueObject;

use App\Domain\Shared\ValueObject\Trait\IntTrait;
use DomainException;
use Stringable;

class Id implements Stringable
{
    use IntTrait;

    const MIN = 900000;
    const MAX = 999999;

    /**
     * @param ?string $value
     */
    public function __construct(
        private readonly ?string $value,
    ) {
        if ($value === null || $value === '') {
            return null;
        }

        if ($value !== null && !preg_match('/^\d+$/', $value)) {
            throw new DomainException(
                self::class . ' value integer format Error'
                . '[value: ' . mb_strimwidth($value, 0, 200, '...') . ']',
            );
        }

        if ((int)$value < self::MIN || (int)$value > self::MAX) {
            throw new DomainException(
                self::class . ' value range Error'
                . '[value: ' . mb_strimwidth($value, 0, 200, '...') . ']',
            );
        }
    }

    /**
     * @return int|null
     */
    public function toInt(): ?int
    {
        return $this->value === null ? null : (int)$this->value;
    }
}
