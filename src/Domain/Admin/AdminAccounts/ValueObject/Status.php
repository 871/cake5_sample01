<?php
declare(strict_types=1);

namespace App\Domain\Admin\AdminAccounts\ValueObject;

use App\Domain\Shared\ValueObject\Trait\IntTrait;
use DomainException;
use Stringable;

class Status implements Stringable
{
    use IntTrait;

    public const ACTIVE = 1;
    public const INACTIVE = 0;

    public const LABELS = [
        self::ACTIVE => '有効',
        self::INACTIVE => '無効',
    ];

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

        if (!in_array((int)$value, [self::ACTIVE, self::INACTIVE], true)) {
            throw new DomainException(
                self::class . ' value Error'
                . '[value: ' . $value . ']',
            );
        }

        $this->value = (int)$value;
    }

    /**
     * @return string
     */
    public function toLabel(): string
    {
        return self::LABELS[$this->value] ?? '';
    }
}
