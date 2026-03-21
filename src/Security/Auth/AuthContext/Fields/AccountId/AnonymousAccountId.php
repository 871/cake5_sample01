<?php
declare(strict_types=1);

namespace App\Security\Auth\AuthContext\Fields\AccountId;

use App\Security\Auth\AuthContext\Fields\AccountId;

class AnonymousAccountId implements AccountId
{
    /**
     * @param string $value
     */
    public function __construct(string $value)
    {
        // 匿名アカウントIDは常に0とする
    }

    /**
     * @return int
     */
    public function toInt(): int
    {
        return 0;
    }

    /**
     * @return string
     */
    public function toString(): string
    {
        return '0';
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->toString();
    }
}
