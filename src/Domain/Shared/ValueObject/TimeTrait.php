<?php
declare(strict_types=1);

namespace App\Domain\Shared\ValueObject;

use DateTime;
use DateTimeImmutable;

trait TimeTrait
{
    /**
     * @var ?DateTimeImmutable
     */
    private readonly ?DateTimeImmutable $value;

    /**
     * @return ?string
     */
    public function format($format = 'H:i:s'): ?string
    {
        return $this->value?->format($format) ?? null;
    }

    /**
     * @return ?string
     */
    public function toString(): string
    {
        return $this->format() ?? '';
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->toString();
    }

    /**
     * @param string $value
     * @param string $format
     */
    protected static function checkFormat(string $value, string $format): bool
    {
        $dt = DateTime::createFromFormat($format, $value);
        return $dt !== false && $dt->format($format) === $value;
    }
}
