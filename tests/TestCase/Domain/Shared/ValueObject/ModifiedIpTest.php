<?php
declare(strict_types=1);

namespace App\Test\TestCase\Domain\Shared\ValueObject;

use App\Domain\Shared\ValueObject\ModifiedIp;
use Cake\TestSuite\TestCase;
use DomainException;
use PHPUnit\Framework\Attributes\DataProvider;

final class ModifiedIpTest extends TestCase
{
    #[DataProvider('validValues')]
    public function testValidValues(?string $value): void
    {
        $vo = new ModifiedIp($value);

        $this->assertSame($value ?? '', $vo->toString());
        $this->assertSame($value ?? '', (string)$vo);
    }

    #[DataProvider('invalidValues')]
    public function testInvalidValues(string $value): void
    {
        $this->expectException(DomainException::class);

        new ModifiedIp($value);
    }

    /**
     * @return array<string, array{0: ?string}>
     */
    public static function validValues(): array
    {
        return [
            'null' => [null],
            'ipv4' => ['192.168.1.1'],
            'ipv6' => ['2001:0db8:85a3:0000:0000:8a2e:0370:7334'],
            'max length 45 chars' => [str_repeat('a', ModifiedIp::MAX_LENGTH)],
        ];
    }

    /**
     * @return array<string, array{0: string}>
     */
    public static function invalidValues(): array
    {
        return [
            'over max length' => [str_repeat('a', ModifiedIp::MAX_LENGTH + 1)],
        ];
    }
}
