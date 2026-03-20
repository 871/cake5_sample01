<?php
declare(strict_types=1);

namespace App\Security\Auth\AuthContext\Fields\AccountName;

use App\Security\Auth\AuthContext\Fields\AccountName;
use App\Domain\Admin\AdminAccounts\ValueObject as Vo;
use DomainException;

class AnonymousAccountName implements AccountName
{
    /**
     * @var string
     */
    private string $value = 'Anonymous';

    public function __construct(string $value = '')
    {
        // 処理なし
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
