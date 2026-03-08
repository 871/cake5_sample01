<?php
declare(strict_types=1);

namespace App\Test\TestCase\Service\Controller\Shared\Process\Process\Fields;

use ArrayIterator;
use App\Service\Controller\Shared\Process\Process\Fields\ProcessParams;
use DomainException;
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
        $this->expectException(DomainException::class);
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

    public function testSetParamUpdatesNestedValue(): void
    {
        $params = new ProcessParams([
            'user' => [
                'name' => 'Alice',
                'age' => 30,
            ],
        ]);

        $newParams = $params->setParam('user.age', 31);

        // 元の値は不変
        $this->assertSame(30, $params->getParam('user.age'));

        // 新しい値
        $this->assertSame(31, $newParams->getParam('user.age'));
    }

    public function testSetParamThrowsExceptionIfRootKeyNotExists(): void
    {
        $this->expectException(DomainException::class);
        $this->expectExceptionMessageMatches('/Process Param not fund/');

        $params = new ProcessParams([
            'user' => [
                'name' => 'Alice',
            ],
        ]);

        $params->setParam('unknown.name', 'Bob');
    }

    public function testGetParamReturnsValue(): void
    {
        $params = new ProcessParams([
            'user' => [
                'name' => 'Alice',
            ],
        ]);

        $this->assertSame('Alice', $params->getParam('user.name'));
    }

    public function testGetParamThrowsExceptionWhenMissing(): void
    {
        $this->expectException(DomainException::class);
        $this->expectExceptionMessageMatches('/Process Param not fund/');

        $params = new ProcessParams([
            'user' => [
                'name' => 'Alice',
            ],
        ]);

        $params->getParam('user.age');
    }

    public function testHasParamReturnsTrueWhenValueMatches(): void
    {
        $params = new ProcessParams([
            'user' => [
                'name' => 'Alice',
            ],
        ]);

        $this->assertTrue($params->hasParam('user.name', 'Alice'));
    }

    public function testHasParamReturnsFalseWhenValueDifferent(): void
    {
        $params = new ProcessParams([
            'user' => [
                'name' => 'Alice',
            ],
        ]);

        $this->assertFalse($params->hasParam('user.name', 'Bob'));
    }
}
