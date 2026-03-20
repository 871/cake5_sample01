<?php
declare(strict_types=1);

namespace App\Security\Auth\AuthContext\Fields\AccountStatusMasterName;

use App\Security\Auth\AuthContext\Fields\AccountStatusMasterName;
use App\Domain\Admin\AdminAccounts\ValueObject as Vo;
use DomainException;

class AdminAccountStatusMasterName implements AccountStatusMasterName
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
        $this->value = (new Vo\AccountStatusMasterName($value))->toStringOrNull() ?? throw new DomainException(
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
