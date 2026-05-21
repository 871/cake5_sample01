<?php
declare(strict_types=1);

namespace App\Domain\Admin\AdminGrant\ValueObject;

use App\Domain\Shared\ValueObject\Trait\IntTrait;
use DomainException;
use Stringable;

class IsActive implements Stringable
{
    use IntTrait;

    public const INACTIVE = 0;
    public const ACTIVE = 1;
    public const VALUES = [self::INACTIVE, self::ACTIVE];

    private ?int $value;

    /**
     * @param ?string $value
     */
    public function __construct(?string $value)
    {
        if ($value === null || $value === '') {
            $this->value = null;

            return;
        }

        if (!preg_match('/^\d+$/', $value) || !in_array((int)$value, self::VALUES, true)) {
            throw new DomainException(
                self::class . ' value out of range Error'
                . '[value: ' . $value . ']'
                . '[allowed: 0, 1]',
            );
        }

        $this->value = (int)$value;
    }
}
