<?php
declare(strict_types=1);

namespace App\Service\Controller\Shared\Process\Process\Fields;

use ArrayIterator;
use DomainException;
use IteratorAggregate;
use JsonSerializable;

final class ProcessParams implements IteratorAggregate, JsonSerializable
{
    /**
     * @param array<int|string, mixed> $values
     */
    public function __construct(
        private readonly array $values,
    ) {
    }

    /**
     * @param array<int|string, mixed> $overrides
     * @return self
     */
    public function with(array $overrides = []): self
    {
        // 存在しないキーが指定された場合は例外
        array_diff_key($overrides, $this->values) === []
            || throw new DomainException(
                'There are no overridable'
                . '[Invalid parameters: ' . print_r(array_diff_key($overrides, $this->values), true) . ' ]'
                . '[allowParamsKeys: ' . print_r(array_keys($this->values), true) . ']',
            );

        return new self(
            array_merge($this->values, $overrides),
        );
    }

    /**
     * @return \ArrayIterator
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->values);
    }

    /**
     * @return array<int|string, mixed>
     */
    public function toArray(): array
    {
        return $this->values;
    }

    /**
     * @return array<int|string, mixed>
     */
    public function jsonSerialize(): array
    {
        return $this->values;
    }
}
