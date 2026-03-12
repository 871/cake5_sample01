<?php
declare(strict_types=1);

namespace App\Service\Input\Normalizer;

use App\Service\Input\InputNormalizer;

final class RecursiveEmptyStringToNullNormalizer implements InputNormalizer
{
    /**
     * @param mixed $input
     * @return mixed
     */
    public function normalize(mixed $input): mixed
    {
        if (is_array($input)) {
            return array_map([$this, 'normalize'], $input);
        }

        return $input === '' ? null : $input;
    }
}
