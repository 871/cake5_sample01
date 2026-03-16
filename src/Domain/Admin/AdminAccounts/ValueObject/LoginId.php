<?php
declare(strict_types=1);

namespace App\Domain\Admin\AdminAccounts\ValueObject;

use App\Domain\Shared\ValueObject\Trait\StringTrait;
use DomainException;
use Stringable;

class LoginId implements Stringable
{
    use StringTrait;

    public const MAX_LENGTH = 100;
    public const MATCH = '/^[a-zA-Z0-9_\-\.@]{1,100}$/';

    /**
     * @param ?string $value
     */
    public function __construct(
        private readonly ?string $value,
    ) {
        if ($value !== null && !preg_match(self::MATCH, $value)) {
            throw new DomainException(
                self::class . ' value format Error'
                . '[value: ' . mb_strimwidth($value, 0, 200, '...') . ']',
            );
        }
    }
}
