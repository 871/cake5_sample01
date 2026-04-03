<?php
declare(strict_types=1);

namespace App\Domain\Log\PageAccessLogs\ValueObject\Search;

use App\Domain\Shared\ValueObject\Trait\DateTimeTrait;
use DateTimeImmutable;
use DateTimeInterface;
use DomainException;
use Stringable;

class CursorAccessed implements Stringable
{
    use DateTimeTrait;

    /**
     * @var ?\DateTimeInterface
     */
    private readonly ?DateTimeInterface $value;

    /**
     * @param ?string $value
     */
    public function __construct(?string $value)
    {
        $dt = DateTimeImmutable::createFromFormat('Y-m-d\TH:i:s.u', $value)
            ?: DateTimeImmutable::createFromFormat('Y-m-d\TH:i:s', $value)
            ?: null;

        if ($dt === null && $value !== null) {
            throw new DomainException(
                self::class . ' value datetime format Error'
                . '[value: ' . $value . ']',
            );
        }

        $this->value = $dt;
    }
}
