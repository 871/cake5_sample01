<?php
declare(strict_types=1);

namespace App\Test\TestCase\Domain\Shared\ValueObject;

use App\Domain\Shared\ValueObject\CreatedBy;
use Cake\TestSuite\TestCase;
use DomainException;
use PHPUnit\Framework\Attributes\DataProvider;

final class CreatedByTest extends TestCase
{
    #[DataProvider('validValues')]
    public function testValidValues(?string $value, ?int $expected): void
    {
        $vo = new CreatedBy($value);

        $this->assertSame((int)$expected, $vo->toInt());
        $this->assertSame($expected, $vo->toIntOrNull());
        $this->assertSame((string)$expected, $vo->toString());
        $this->assertSame($expected===null ? null : (string)$expected, $vo->toStringOrNull());
        $this->assertSame((string)$expected, (string)$vo);
    }

    #[DataProvider('invalidValues')]
    public function testInvalidValues(string $value): void
    {
        $this->expectException(DomainException::class);

        new CreatedBy($value);
    }

    /**
     * @return array<string, array{0: ?string, 1: ?int}>
     */
    public static function validValues(): array
    {
        return [
            'null' => [null, null],
            'empty string' => ['', null],
            'min' => ['1', 1],
            'middle' => ['12345', 12345],
            'large bigint' => ['9223372036854775807', 9223372036854775807],
        ];
    }

    /**
     * @return array<string, array{0: string}>
     */
    public static function invalidValues(): array
    {
        return [
            'negative' => ['-1'],
            'float' => ['1.5'],
            'string' => ['abc'],
            'mixed' => ['123abc'],
        ];
    }
}
