<?php
declare(strict_types=1);

namespace App\Domain\Shared\ValueObject\Trait;

use DateTime;
use DateTimeImmutable;

trait DateTrait
{
    /**
     * @var ?\DateTimeImmutable
     */
    private readonly ?DateTimeImmutable $value;

    /**
     * @param string $format
     * @return ?string
     */
    public function format(string $format = 'Y-m-d'): ?string
    {
        return $this->value?->format($format);
    }

    /**
     * @return string
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
     * @param ?string $value
     * @param string $format
     * @return self
     */
    public static function fromString(?string $value, string $format = 'Y-m-d'): self
    {
        return new self(
            $value === null || $value === '' ? null : $value,
            $format,
        );
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
