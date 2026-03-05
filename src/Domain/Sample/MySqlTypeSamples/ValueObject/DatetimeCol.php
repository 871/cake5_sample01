<?php
declare(strict_types=1);

namespace App\Domain\Sample\MySqlTypeSamples\ValueObject;

use App\Domain\Shared\ValueObject\DatetimeTrait;
use DateTimeImmutable;
use DomainException;
use Stringable;

class DatetimeCol implements Stringable
{
    use DatetimeTrait;

    public const MIN = '1970-01-01 00:00:00';
    public const MAX = '2999-12-31 23:59:59';

    /**
     * @var ?\DateTimeImmutable
     */
    private readonly ?DateTimeImmutable $value;

    /**
     * @param ?string $value
     */
    public function __construct(?string $value, string $format = 'Y-m-d H:i:s')
    {
        if ($value === null) {
            $this->value = null;

            return;
        }

        if (!static::checkFormat($value, $format)) {
            throw new DomainException(
                self::class . ' value date format Error'
                    . '[value: ' . $value . ']'
                    . '[format: ' . $format . ']',
            );
        }

        $minDate = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', self::MIN);
        $maxDate = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', self::MAX);
        $resultValue = DateTimeImmutable::createFromFormat($format, $value);
        if ($resultValue === false || $resultValue < $minDate || $resultValue > $maxDate) {
            throw new DomainException(
                self::class . ' value date range Error'
                    . '[value: ' . $value . ']'
                    . '[min: ' . self::MIN . ']'
                    . '[max: ' . self::MAX . ']',
            );
        }
        $this->value = $resultValue;
    }
}
