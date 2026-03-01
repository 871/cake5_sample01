<?php
declare(strict_types=1);

namespace App\Test\TestCase\Service\Controller\Shared\Process\Process\Fields;

use ArrayIterator;
use App\Service\Controller\Shared\Process\Process\Fields\ProcessParams;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class ProcessParamsTest extends TestCase
{
    public function testCanCreateAndAccessValues(): void
    {
        $data = [
            'name' => 'Alice',
            'age' => 30,
        ];

        $params = new ProcessParams($data);

        // toArray() returns original array
        $this->assertSame($data, $params->toArray());

        // jsonSerialize() returns same array
        $this->assertSame($data, $params->jsonSerialize());

        // getIterator() returns ArrayIterator with same data
        $iterator = $params->getIterator();
        $this->assertInstanceOf(ArrayIterator::class, $iterator);
        $this->assertSame($data, $iterator->getArrayCopy());
    }

    public function testWithOverridesValues(): void
    {
        $params = new ProcessParams([
            'name' => 'Alice',
            'age' => 30,
        ]);

        $newParams = $params->with(['age' => 35]);

        // 元の値は変更されない
        $this->assertSame(30, $params->toArray()['age']);

        // 上書きされた値が反映される
        $this->assertSame(35, $newParams->toArray()['age']);
    }

    public function testWithThrowsExceptionOnUnknownKeys(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/There are no overridable/');

        $params = new ProcessParams([
            'name' => 'Alice',
            'age' => 30,
        ]);

        // 存在しないキーを指定
        $params->with(['unknown' => 'value']);
    }

    public function testCanIterate(): void
    {
        $data = ['x' => 1, 'y' => 2];
        $params = new ProcessParams($data);

        $collected = [];
        foreach ($params as $k => $v) {
            $collected[$k] = $v;
        }

        $this->assertSame($data, $collected);
    }
}
