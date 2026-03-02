<?php
declare(strict_types=1);

namespace App\Test\TestCase\Service\Input\Normalizer;

use App\Service\Input\Normalizer\RecursiveEmptyStringToNullNormalizer;
use PHPUnit\Framework\TestCase;

final class RecursiveEmptyStringToNullNormalizerTest extends TestCase
{
    private RecursiveEmptyStringToNullNormalizer $normalizer;

    protected function setUp(): void
    {
        $this->normalizer = new RecursiveEmptyStringToNullNormalizer();
    }

    public function testEmptyStringIsConvertedToNull(): void
    {
        $this->assertNull(
            $this->normalizer->normalize('')
        );
    }

    public function testNonEmptyStringIsNotModified(): void
    {
        $this->assertSame(
            'abc',
            $this->normalizer->normalize('abc')
        );
    }

    public function testZeroStringIsNotConverted(): void
    {
        $this->assertSame(
            '0',
            $this->normalizer->normalize('0')
        );
    }

    public function testScalarValuesAreNotModified(): void
    {
        $this->assertSame(0, $this->normalizer->normalize(0));
        $this->assertSame(false, $this->normalizer->normalize(false));
        $this->assertSame(null, $this->normalizer->normalize(null));
    }

    public function testArrayIsRecursivelyNormalized(): void
    {
        $input = [
            'name' => '',
            'age' => '20',
            'profile' => [
                'nickname' => '',
                'tags' => ['php', '', 'cake'],
            ],
        ];

        $expected = [
            'name' => null,
            'age' => '20',
            'profile' => [
                'nickname' => null,
                'tags' => ['php', null, 'cake'],
            ],
        ];

        $this->assertSame(
            $expected,
            $this->normalizer->normalize($input)
        );
    }

    public function testEmptyArrayIsReturnedAsIs(): void
    {
        $this->assertSame(
            [],
            $this->normalizer->normalize([])
        );
    }
}
