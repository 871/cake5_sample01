<?php
declare(strict_types=1);

namespace App\Domain\Admin\AdminAccounts\ValueObject;

use App\Domain\Shared\ValueObject\Trait\StringTrait;
use DomainException;
use Stringable;

class Email implements Stringable
{
    use StringTrait;

    public const MAX_LENGTH = 255;

    /**
     * @param ?string $value
     */
    public function __construct(
        private readonly ?string $value,
    ) {
        if ($value !== null && filter_var($value, FILTER_VALIDATE_EMAIL) === false) {
            throw new DomainException(
                self::class . ' value email format Error'
                . '[value: ' . mb_strimwidth($value, 0, 200, '...') . ']',
            );
        }
    }
}
