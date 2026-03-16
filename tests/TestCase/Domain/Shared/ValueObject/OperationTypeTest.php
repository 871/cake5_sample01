<?php
declare(strict_types=1);

namespace App\Test\TestCase\Domain\Shared\ValueObject;

use App\Domain\Shared\ValueObject\OperationType;
use Cake\TestSuite\TestCase;
use DomainException;
use PHPUnit\Framework\Attributes\DataProvider;

final class OperationTypeTest extends TestCase
{
    #[DataProvider('validValues')]
    public function testValidValues(?string $value): void
    {
        $vo = new OperationType($value);

        $this->assertSame($value ?? '', $vo->toString());
        $this->assertSame($value ?? '', (string)$vo);
    }

    #[DataProvider('invalidValues')]
    public function testInvalidValues(string $value): void
    {
        $this->expectException(DomainException::class);

        new OperationType($value);
    }

    /**
     * @return array<string, array{0: ?string}>
     */
    public static function validValues(): array
    {
        return [
            'null' => [null],
            'INSERT' => [OperationType::INSERT],
            'UPDATE' => [OperationType::UPDATE],
            'DELETE' => [OperationType::DELETE],
        ];
    }

    /**
     * @return array<string, array{0: string}>
     */
    public static function invalidValues(): array
    {
        return [
            'lowercase insert' => ['insert'],
            'SELECT' => ['SELECT'],
            'empty string' => [''],
            'invalid value' => ['UPSERT'],
        ];
    }
}
