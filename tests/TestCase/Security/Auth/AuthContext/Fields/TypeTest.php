<?php
declare(strict_types=1);

namespace App\Test\TestCase\Security\Auth\AuthContext\Fields;

use App\Security\Auth\AuthContext;
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

    public static function validTypes(): array
    {
        return [
            'anonymous' => [AuthContext::TYPE_ANONYMOUS],
            'customer'  => [AuthContext::TYPE_CUSTMER],
            'user'      => [AuthContext::TYPE_USER],
            'admin'     => [AuthContext::TYPE_ADMIN],
        ];
    }

    public static function invalidTypes(): array
    {
        return [
            'empty' => [''],
            'unknown' => ['hacker'],
            'case mismatch' => [strtoupper(AuthContext::TYPE_USER)],
            'whitespace' => [' '],
        ];
    }
}
