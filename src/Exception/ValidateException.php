<?php
declare(strict_types=1);

namespace App\Exception;

use Cake\Utility\Hash;
use Exception;

class ValidateException extends Exception
{
    /**
     * @param array<string, array<string, string|array<int|string, mixed>>> $errorInfos
     */
    public function __construct(
        /** @var array<string, array<string, string|array<int|string, mixed>>> */
        private readonly array $errorInfos,
    ) {
        // 処理なし
    }

    /**
     * @return array<string>
     */
    public function getErrorMeesasges(): array
    {
        return array_values(
            array_unique(
                Hash::flatten($this->errorInfos),
            ),
        );
    }

    /**
     * @return array<string>
     */
    public function getErrorFields(): array
    {
        return array_map(fn() => 'error', $this->errorInfos);
    }
}
