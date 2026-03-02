<?php
declare(strict_types=1);

namespace App\Test\TestCase\Security\Auth\AuthContext\Fields;

use App\Security\Auth\AuthContext\Fields\AccountId;
use Cake\TestSuite\TestCase;
use DomainException;
use PHPUnit\Framework\Attributes\DataProvider;

final class AccountIdTest extends TestCase
{
    #[DataProvider('validValues')]
    public function testValidValues(?int $value): void
    {
        $accountId = new AccountId($value);

        $this->assertSame($value, $accountId->toInt());
        $this->assertSame((string)$value, $accountId->toString());
        $this->assertSame((string)$value, (string)$accountId);
    }

    #[DataProvider('invalidValues')]
    public function testInvalidValues(int $value): void
    {
        $this->expectException(DomainException::class);

        new AccountId($value);
    }

    public static function validValues(): array
    {
        return [
            'null' => [null],
            'min' => [1],
            'middle' => [100],
            'max' => [2147483647], // AccountId::MAXの値
        ];
    }

    public static function invalidValues(): array
    {
        return [
            'zero' => [0],
            'negative' => [-1],
            'over max' => [2147483647 + 1], // AccountId::MAXの値 + 1
        ];
    }
}
