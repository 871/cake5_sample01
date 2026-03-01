<?php
declare(strict_types=1);

namespace App\Domain\Sample\MySqlTypeSamples;

use DateTimeInterface;

class SearchCondition
{
    public function __construct(
        private readonly ?string $id,
        private readonly ?int $intColFrom,
        private readonly ?int $intColTo,
        private readonly ?int $bigintColFrom,
        private readonly ?int $bigintColTo,
        private readonly ?float $decimalColFrom,
        private readonly ?float $decimalColTo,
        private readonly ?float $floatColFrom,
        private readonly ?float $floatColTo,
        private readonly ?float $doubleColFrom,
        private readonly ?float $doubleColTo,
        private readonly ?DateTimeInterface $dateColFrom,
        private readonly ?DateTimeInterface $dateColTo,
        private readonly ?DateTimeInterface $timeColFrom,
        private readonly ?DateTimeInterface $timeColTo,
        private readonly ?DateTimeInterface $datetimeColFrom,
        private readonly ?DateTimeInterface $datetimeColTo,
        private readonly ?string $keyword,
    ) {
        // 処理なし
    }

    /**
     * @return ?string
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * @return ?int
     */
    public function getIntColFrom(): ?int
    {
        return $this->intColFrom;
    }

    /**
     * @return ?int
     */
    public function getIntColTo(): ?int
    {
        return $this->intColTo;
    }

    /**
     * @return ?int
     */
    public function getBigintColFrom(): ?int
    {
        return $this->bigintColFrom;
    }

    /**
     * @return ?int
     */
    public function getBigintColTo(): ?int
    {
        return $this->bigintColTo;
    }

    /**
     * @return ?float
     */
    public function getDecimalColFrom(): ?float
    {
        return $this->decimalColFrom;
    }

    /**
     * @return ?float
     */
    public function getDecimalColTo(): ?float
    {
        return $this->decimalColTo;
    }

    /**
     * @return ?float
     */
    public function getFloatColFrom(): ?float
    {
        return $this->floatColFrom;
    }

    /**
     * @return ?float
     */
    public function getFloatColTo(): ?float
    {
        return $this->floatColTo;
    }

    /**
     * @return ?float
     */
    public function getDoubleColFrom(): ?float
    {
        return $this->doubleColFrom;
    }

    /**
     * @return ?float
     */
    public function getDoubleColTo(): ?float
    {
        return $this->doubleColTo;
    }

    /**
     * @return ?DateTimeInterface
     */
    public function getDateColFrom(): ?DateTimeInterface
    {
        return $this->dateColFrom;
    }

    /**
     * @return ?DateTimeInterface
     */
    public function getDateColTo(): ?DateTimeInterface
    {
        return $this->dateColTo;
    }

    /**
     * @return ?DateTimeInterface
     */
    public function getTimeColFrom(): ?DateTimeInterface
    {
        return $this->timeColFrom;
    }

    /**
     * @return ?DateTimeInterface
     */
    public function getTimeColTo(): ?DateTimeInterface
    {
        return $this->timeColTo;
    }

    /**
     * @return ?DateTimeInterface
     */
    public function getDatetimeColFrom(): ?DateTimeInterface
    {
        return $this->datetimeColFrom;
    }

    /**
     * @return ?DateTimeInterface
     */
    public function getDatetimeColTo(): ?DateTimeInterface
    {
        return $this->datetimeColTo;
    }

    /**
     * @return ?string
     */
    public function getKeyword(): ?string
    {
        return $this->keyword;
    }
}
