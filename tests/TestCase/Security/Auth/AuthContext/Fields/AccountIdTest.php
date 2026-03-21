<?php
declare(strict_types=1);

namespace App\Test\TestCase\Security\Auth\AuthContext\Fields;

use App\Security\Auth\AuthContext\Fields\AccountId\AdminAccountId;
use App\Security\Auth\AuthContext\Fields\AccountId\AnonymousAccountId;
use Cake\TestSuite\TestCase;
use DomainException;
use PHPUnit\Framework\Attributes\DataProvider;

final class AccountIdTest extends TestCase
{
    // --- AdminAccountId tests ---

    #[DataProvider('validAdminValues')]
    public function testAdminAccountIdValidValues(string $value, int $expected): void
    {
        $accountId = new AdminAccountId($value);

        $this->assertSame($expected, $accountId->toInt());
        $this->assertSame((string)$expected, $accountId->toString());
        $this->assertSame((string)$expected, (string)$accountId);
    }

    #[DataProvider('invalidAdminValues')]
    public function testAdminAccountIdInvalidValues(string $value): void
    {
        $this->expectException(DomainException::class);

        new AdminAccountId($value);
    }

    /**
     * @return array<string, array{0: string, 1: int}>
     */
    public static function validAdminValues(): array
    {
        return [
            'min' => ['900000', 900000],
            'middle' => ['950000', 950000],
            'max' => ['999999', 999999],
        ];
    }

    /**
     * @return array<string, array{0: string}>
     */
    public static function invalidAdminValues(): array
    {
        return [
            'below min' => ['899999'],
            'above max' => ['1000000'],
            'zero' => ['0'],
            'negative' => ['-1'],
            'non-numeric' => ['abc'],
        ];
    }

    // --- AnonymousAccountId tests ---

    public function testAnonymousAccountIdAlwaysReturnsZero(): void
    {
        $accountId = new AnonymousAccountId('0');

        $this->assertSame(0, $accountId->toInt());
        $this->assertSame('0', $accountId->toString());
        $this->assertSame('0', (string)$accountId);
    }

    public function testAnonymousAccountIdIgnoresInput(): void
    {
        $accountId = new AnonymousAccountId('anything');

        $this->assertSame(0, $accountId->toInt());
        $this->assertSame('0', $accountId->toString());
    }
}
