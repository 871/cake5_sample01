<?php
declare(strict_types=1);

namespace App\Application\Input\Pipeline;

use App\Application\Input\InputNormalizer;
use InvalidArgumentException;

final class InputNormalizationPipeline
{
    /**
     * @param array<int, mixed> $normalizers
     */
    public function __construct(private array $normalizers)
    {
        array_walk($this->normalizers, function ($normalizer): void {

            $normalizer instanceof InputNormalizer
                ?: throw new InvalidArgumentException(
                    'Not InputNormalizer Instance'
                    . '[' . print_r($normalizer, true) . ']',
                );
        });
    }

    /**
     * @param mixed $input
     * @return mixed
     */
    public function process(mixed $input): mixed
    {
        foreach ($this->normalizers as $normalizer) {
            /** @var \App\Application\Input\InputNormalizer $normalizer */
            $input = $normalizer->normalize($input);
        }

        return $input;
    }
}
