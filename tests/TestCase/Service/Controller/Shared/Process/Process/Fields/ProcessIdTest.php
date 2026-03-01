<?php
declare(strict_types=1);

namespace App\Test\TestCase\Service\Controller\Shared\Process\Process\Fields;

use App\Service\Controller\Shared\Process\Process\Fields\ProcessId;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class ProcessIdTest extends TestCase
{
    public function testCanCreateWithValidAscii(): void
    {
        $id = new ProcessId('abc123');
        $this->assertInstanceOf(ProcessId::class, $id);
        $this->assertSame('000000000abc123', $id->toString());
        $this->assertSame('000000000abc123', (string)$id);
    }

    public function testThrowsExceptionWhenTooLong(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('process_id length Error');

        new ProcessId('abcdefghijklmnop'); // 16文字, LENGTH=15
    }

    public function testThrowsExceptionWhenNonAscii(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('process_id length Error');

        new ProcessId('abc123あいう'); // 全角文字を含む
    }

    public function testRightPaddingZeroes(): void
    {
        $id = new ProcessId('1');
        $this->assertSame(str_repeat('0', ProcessId::LENGTH - 1) . '1', $id->toString());
    }
}
