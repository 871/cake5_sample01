<?php
declare(strict_types=1);

namespace App\Test\TestCase\Domain\Shared\ValueObject;

use App\Domain\Shared\ValueObject\HistoryCreated;
use Cake\TestSuite\TestCase;
use DomainException;
use PHPUnit\Framework\Attributes\DataProvider;

final class HistoryCreatedTest extends TestCase
{
    #[DataProvider('validValues')]
    public function testValidValues(?string $value, ?string $expected): void
    {
        $vo = new HistoryCreated($value);

        $this->assertSame($expected, $vo->format());
        $this->assertSame($expected ?? '', $vo->toString());
        $this->assertSame($expected ?? '', (string)$vo);
    }

    #[DataProvider('invalidValues')]
    public function testInvalidValues(string $value): void
    {
        $this->expectException(DomainException::class);

        new HistoryCreated($value);
    }

    /**
     * @return array<string, array{0: ?string, 1: ?string}>
     */
    public static function validValues(): array
    {
        return [
            'null' => [null, null],
            'valid datetime' => ['2024-01-15T12:30:00', '2024-01-15T12:30:00'],
            'boundary start of day' => ['2024-01-01T00:00:00', '2024-01-01T00:00:00'],
            'boundary end of day' => ['2024-12-31T23:59:59', '2024-12-31T23:59:59'],
        ];
    }

    /**
     * @return array<string, array{0: string}>
     */
    public static function invalidValues(): array
    {
        return [
            'invalid format' => ['2024-01-15 12:30:00'],
            'date only' => ['2024-01-15'],
            'invalid date' => ['not-a-date'],
            'invalid month' => ['2024-13-01T00:00:00'],
        ];
    }
}
