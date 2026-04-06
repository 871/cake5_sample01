<?php
declare(strict_types=1);

namespace App\Domain\Log\PageAccessLogs\ValueObject\Search;

use App\Domain\Log\PageAccessLogs\ValueObject as Vo;
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
        if ($value === null || $value === '') {
            $this->value = null;
            return;
        }

        $this->value = (new Vo\Accessed($value))->toDateTime();
    }
}
