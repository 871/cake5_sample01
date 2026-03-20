<?php
declare(strict_types=1);

namespace App\Security\Auth\AuthContext\Fields\AccountStatusMasterId;

use App\Security\Auth\AuthContext\Fields\AccountStatusMasterId;
use App\Domain\Admin\AdminAccounts\ValueObject as Vo;
use DomainException;

class AdminAccountStatusMasterId implements AccountStatusMasterId
{
    /**
     * @var int
     */
    private int $value;
    
    /**
     * @param string $value
     */
    public function __construct(string $value)
    {
        $this->value = (new Vo\AccountStatusMasterId($value))->toIntOrNUll() ?? throw new DomainException(
            self::class . ' Generate Error'
            . '[value: ' . (string)$value . ']',
        );
    }

    /**
     * @return int
     */
    public function toInt(): int
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function toString(): string
    {
        return (string)$this->value;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->toString();
    }
}
