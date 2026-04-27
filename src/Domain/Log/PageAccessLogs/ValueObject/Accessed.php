<?php
declare(strict_types=1);

namespace App\Domain\Log\PageAccessLogs\ValueObject;

use App\Domain\Shared\ValueObject\Trait\DateTimeTrait;
use DateTimeImmutable;
use DomainException;
use Stringable;

class Accessed implements Stringable
{
    use DateTimeTrait;

    /**
     * @param string $value
     */
    public function __construct(string $value)
    {
        $dt = DateTimeImmutable::createFromFormat('Y-m-d\TH:i:s.u', $value)
            ?: DateTimeImmutable::createFromFormat('Y-m-d\TH:i:s', $value)
            ?: null;

        if ($dt === null) {
            throw new DomainException(
                self::class . ' value datetime format Error'
                . '[value: ' . $value . ']',
            );
        }

        $this->value = $dt;
    }

    /**
     * @param ?string $value
     * @param string $format
     * @return self
     */
    public static function fromString(?string $value, string $format = 'Y-m-d\TH:i:s'): self
    {
        if ($value === null || $value === '') {
            throw new DomainException(
                self::class . ' value is required',
            );
        }

        return new self($value);
    }
}
