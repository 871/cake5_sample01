<?php
declare(strict_types=1);

namespace App\Domain\Admin\AdminAccounts\ValueObject;

use App\Domain\Shared\ValueObject\Trait\StringTrait;
use Stringable;

class Password implements Stringable
{
    use StringTrait;

    public const MIN_LENGTH = 8;
    public const MAX_LENGTH = 100;

    /**
     * @param ?string $value
     */
    public function __construct(
        private readonly ?string $value,
    ) {
        // パスワードはハッシュ済みの値もそのまま許容するため、長さの検証のみ行う
    }

    /**
     * パスワードをハッシュ化して返す
     *
     * @return string
     */
    public function toHash(): string
    {
        return password_hash($this->value ?? '', PASSWORD_DEFAULT);
    }
}
