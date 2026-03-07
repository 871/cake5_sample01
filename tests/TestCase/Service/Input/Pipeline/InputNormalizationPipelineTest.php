<?php
declare(strict_types=1);

namespace App\Test\TestCase\Service\Input\Pipeline;

use App\Service\Input\InputNormalizer;
use App\Service\Input\Pipeline\InputNormalizationPipeline;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class InputNormalizationPipelineTest extends TestCase
{
    public function testNormalizersAreExecutedInOrder(): void
    {
        $normalizer1 = $this->createMock(InputNormalizer::class);
        $normalizer2 = $this->createMock(InputNormalizer::class);

        $normalizer1
            ->expects($this->once())
            ->method('normalize')
            ->with('input')
            ->willReturn('step1');

        $normalizer2
            ->expects($this->once())
            ->method('normalize')
            ->with('step1')
            ->willReturn('step2');

        $pipeline = new InputNormalizationPipeline([
            $normalizer1,
            $normalizer2,
        ]);

        $result = $pipeline->process('input');

        $this->assertSame('step2', $result);
    }

    public function testEmptyNormalizerArrayIsAllowed(): void
    {
        $pipeline = new InputNormalizationPipeline([]);

        $input = ['a' => 1];

        $this->assertSame($input, $pipeline->process($input));
    }

    public function testThrowsExceptionIfInvalidNormalizerIsPassed(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Not InputNormalizer Instance');

        new InputNormalizationPipeline([
            new \stdClass(), // 不正オブジェクト
        ]);
    }

    public function testExceptionInsideNormalizerIsPropagated(): void
    {
        $normalizer = $this->createMock(InputNormalizer::class);

        $normalizer
            ->expects($this->once())
            ->method('normalize')
            ->willThrowException(new \RuntimeException('error'));

        $pipeline = new InputNormalizationPipeline([$normalizer]);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('error');

        $pipeline->process('input');
    }
}
