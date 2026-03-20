<?php
declare(strict_types=1);

namespace App\Security\Auth\AuthContext\Fields;

use Stringable;

interface AccountStatusMasterName extends Stringable
{
    public function __construct(string $value);

    /**
     * @return string
     */
    public function toString(): string;

    /**
     * @return string
     */
    public function __toString(): string;
}
