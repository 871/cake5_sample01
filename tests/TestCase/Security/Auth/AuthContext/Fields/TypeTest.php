<?php
declare(strict_types=1);

namespace App\Test\TestCase\Security\Auth\AuthContext\Fields;

use App\Security\Auth\AuthContext\Fields\Type;
use Cake\TestSuite\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Exception;

final class TypeTest extends TestCase
{
    #[DataProvider('validTypes')]
    public function testValidTypes(string $value): void
    {
        $type = new Type($value);

        $this->assertSame($value, $type->toString());
        $this->assertSame($value, (string)$type);
    }

    #[DataProvider('invalidTypes')]
    public function testInvalidTypes(string $value): void
    {
        $this->expectException(Exception::class);

        new Type($value);
    }

    /**
     * @return array<string, array{0: string}>
     */
    public static function validTypes(): array
    {
        return [
            'anonymous' => [Type::TYPE_ANONYMOUS],
            'customer'  => [Type::TYPE_CUSTMER],
            'user'      => [Type::TYPE_USER],
            'admin'     => [Type::TYPE_ADMIN],
        ];
    }

    /**
     * @return array<string, array{0: string}>
     */
    public static function invalidTypes(): array
    {
        return [
            'empty' => [''],
            'unknown' => ['hacker'],
            'case mismatch' => [strtoupper(Type::TYPE_USER)],
            'whitespace' => [' '],
        ];
    }
}
