<?php
declare(strict_types=1);

namespace App\Test\TestCase\Service\Controller\Shared\Process\Process;

use App\Service\Controller\Shared\Process\Process\Fields\ProcessId;
use App\Service\Controller\Shared\Process\Process\Fields\ProcessParams;
use App\Service\Controller\Shared\Process\Process\InputProcess;
use PHPUnit\Framework\TestCase;

final class InputProcessTest extends TestCase
{
    public function testCanCreateInputProcess(): void
    {
        $processId = new ProcessId('abc123');
        $params = new ProcessParams(['foo' => 'bar']);

        $inputProcess = new InputProcess($processId, $params);

        // getId() returns same ProcessId
        $this->assertSame($processId, $inputProcess->getId());

        // getProcessParams() returns same ProcessParams
        $this->assertSame($params, $inputProcess->getProcessParams());
    }

    public function testSetProcessParamsReturnsNewInstance(): void
    {
        $processId = new ProcessId('abc123');
        $params1 = new ProcessParams(['foo' => 'bar']);
        $params2 = new ProcessParams(['foo' => 'baz']);

        $inputProcess = new InputProcess($processId, $params1);
        $newInputProcess = $inputProcess->setProcessParams($params2);

        // 元のインスタンスは変更されない
        $this->assertSame($params1, $inputProcess->getProcessParams());

        // 新しいインスタンスが返される
        $this->assertNotSame($inputProcess, $newInputProcess);
        $this->assertSame($params2, $newInputProcess->getProcessParams());

        // ProcessId は同じ
        $this->assertSame($processId, $newInputProcess->getId());
    }
}
