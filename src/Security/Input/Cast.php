<?php
declare(strict_types=1);

namespace App\Security\Input;

use DateTimeImmutable;
use DateTimeInterface;
use Exception;

class Cast
{
    /**
     * @param mixed $value
     * @return int|null
     */
    public static function toInt(mixed $value): ?int
    {
        if ($value === null) {
            return null;
        }

        if (is_int($value)) {
            return $value;
        }

        if (is_string($value) && preg_match('/^-?\d+$/', $value)) {
            return (int)$value;
        }

        return null;
    }

    /**
     * @param mixed $value
     * @return float|null
     */
    public static function toFloat(mixed $value): ?float
    {
        if ($value === null) {
            return null;
        }

        if (is_float($value)) {
            return $value;
        }

        if (is_string($value) && preg_match('/^-?\d+(\.\d+)?$/', $value)) {
            return (float)$value;
        }

        return null;
    }

    /**
     * @param mixed $value
     * @return string|null
     */
    public static function toString(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        return is_scalar($value) || (is_object($value) && method_exists($value, '__toString'))
            ? (string)$value : null;
    }

    /**
     * @param mixed $value
     * @return bool|null
     */
    public static function toBool(mixed $value): ?bool
    {
        if ($value === null) {
            return null;
        }

        if (is_bool($value)) {
            return $value;
        }

        if (in_array($value, [0, 1, '0', '1'], true)) {
            return (bool)$value;
        }

        if (is_string($value)) {
            $lowerValue = strtolower($value);
            if ($lowerValue === 'true') {
                return true;
            }
            if ($lowerValue === 'false') {
                return false;
            }
        }

        return null;
    }

    /**
     * @param mixed $value
     * @return \DateTimeInterface|null
     */
    public static function toDateTime(mixed $value): ?DateTimeInterface
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof DateTimeInterface) {
            return $value;
        }

        if (is_string($value)) {
            try {
                return new DateTimeImmutable($value);
            } catch (Exception $e) {
                return null;
            }
        }

        return null;
    }

    /**
     * @param mixed $value
     * @return \DateTimeInterface|null
     */
    public static function toDate(mixed $value): ?DateTimeInterface
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof DateTimeInterface) {
            return new DateTimeImmutable($value->format('Y-m-d'));
        }

        if (is_string($value)) {
            try {
                return new DateTimeImmutable(
                    (new DateTimeImmutable($value))->format('Y-m-d'),
                );
            } catch (Exception $e) {
                return null;
            }
        }

        return null;
    }

    /**
     * @param mixed $value
     * @return \DateTimeInterface|null
     */
    public static function toTime(mixed $value): ?DateTimeInterface
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof DateTimeInterface) {
            return new DateTimeImmutable($value->format('H:i:s'));
        }

        if (is_string($value)) {
            try {
                return new DateTimeImmutable(
                    (new DateTimeImmutable($value))->format('H:i:s'),
                );
            } catch (Exception $e) {
                return null;
            }
        }

        return null;
    }

    /**
     * @param mixed $value
     * @param string $format
     * @return ?string
     */
    public static function toDateTimeString(mixed $value, string $format = 'Y-m-d H:i:s'): ?string
    {
        $dateTime = self::toDateTime($value);
        if ($dateTime === null) {
            return null;
        }

        return $dateTime->format($format);
    }

    /**
     * @param mixed $value
     * @param string $format
     * @return ?string
     */
    public static function toDateString(mixed $value, string $format = 'Y-m-d'): ?string
    {
        $date = self::toDate($value);
        if ($date === null) {
            return null;
        }

        return $date->format($format);
    }

    /**
     * @param mixed $value
     * @param string $format
     * @return ?string
     */
    public static function toTimeString(mixed $value, string $format = 'H:i:s'): ?string
    {
        $time = self::toTime($value);
        if ($time === null) {
            return null;
        }

        return $time->format($format);
    }
}
