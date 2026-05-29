<?php
declare(strict_types=1);

namespace App\Application\Input;

interface InputNormalizer
{
    /**
     * @param mixed $input
     * @return mixed
     */
    public function normalize(mixed $input): mixed;
}
