<?php
declare(strict_types=1);

namespace App\Security\Auth\AuthContext\Fields\AccountStatusMasterCode;

use App\Security\Auth\AuthContext\Fields\AccountStatusMasterCode;
use App\Domain\Admin\AdminAccounts\ValueObject as Vo;
use DomainException;

class AdminAccountStatusMasterCode implements AccountStatusMasterCode
{
    /**
     * @var string
     */
    private string $value;
    
    /**
     * @param string $value
     */
    public function __construct(string $value)
    {
        $this->value = (new Vo\AccountStatusMasterCode($value))->toStringOrNull() ?? throw new DomainException(
            self::class . ' Generate Error'
            . '[value: ' . (string)$value . ']',
        );
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
