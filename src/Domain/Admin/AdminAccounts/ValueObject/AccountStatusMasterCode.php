<?php
declare(strict_types=1);

namespace App\Domain\Admin\AdminAccounts\ValueObject;

use App\Domain\Shared\ValueObject\Trait\IntTrait;
use DomainException;
use Stringable;

class AccountStatusMasterCode implements Stringable
{
    use IntTrait;

    const PENDING = 'PENDING';
    const ACTIVE = 'ACTIVE';
    const SUSPENDED = 'SUSPENDED';
    const LOCKED = 'LOCKED';
    const DELETED = 'DELETED';
    
    private ?string $value;

    /**
     * @param ?string $value
     */
    public function __construct(?string $value)
    {
        if ($value === null || $value === '') {
            $this->value = null;

            return;
        }

        if (in_array($value, [
            self::PENDING, 
            self::ACTIVE, 
            self::SUSPENDED, 
            self::LOCKED, 
            self::DELETED
        ], true) === false) {
            throw new DomainException(
                self::class . ' value type Error'
                . '[value: ' . mb_strimwidth($value, 0, 200, '...') . ']',
            );
        }

        $this->value = $value;
    }
}
