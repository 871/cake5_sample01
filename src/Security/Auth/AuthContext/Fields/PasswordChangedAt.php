<?php
declare(strict_types=1);

namespace App\Security\Auth\AuthContext\Fields;

use Stringable;
use DateTimeInterface;
use DomainException;
use DateTimeImmutable;

class PasswordChangedAt implements Stringable
{
    /**
     * @var \DateTimeInterface
     */
    private readonly DateTimeInterface $value;

    /**
     * @param string $value
     */
    public function __construct(string $value, string $format = 'Y-m-d\TH:i:s') 
    { 
        $datetime = DateTimeImmutable::createFromFormat($format, $value);
        if ($datetime === false || $datetime->format($format) !== $value) {
            throw new DomainException(
                self::class . ' Generate Error'
                . '[value: ' . $value . ']'
                . '[format: ' . $format . ']',
            );
        }

        $this->value = $datetime;
    }

    /**
     * @return \DateTimeInterface
     */
    public function toDateTimeOrNull(): DateTimeInterface
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function toString(): string
    {
        return $this->value->format('Y-m-d\TH:i:s');
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->toString();
    }
}
