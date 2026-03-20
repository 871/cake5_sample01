<?php
declare(strict_types=1);

namespace App\Security\Auth\AuthContext\Fields\AccountName;

use App\Security\Auth\AuthContext\Fields\AccountName;
use App\Domain\Admin\AdminAccounts\ValueObject as Vo;
use DomainException;

class AdminAccountName implements AccountName
{
    /**
     * @var string
     */
    private string $value;

    public function __construct(string $value)
    {
        $this->value = (new Vo\Name($value))->toStringOrNull() ?? throw new DomainException(
            self::class . ' Generate Error'
            . '[value: ' . (string)$value . ']',
        );
    }

    /**
     * @return string
     */
    public function toString(): string
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->toString();
    }
}
