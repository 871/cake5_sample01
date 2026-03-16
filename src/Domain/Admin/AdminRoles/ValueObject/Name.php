<?php
declare(strict_types=1);

namespace App\Domain\Admin\AdminRoles\ValueObject;

use App\Domain\Shared\ValueObject\Trait\StringTrait;
use Stringable;

class Name implements Stringable
{
    use StringTrait;

    public const MAX_LENGTH = 100;

    /**
     * @param ?string $value
     */
    public function __construct(
        private readonly ?string $value,
    ) {
        // 処理なし
    }
}
