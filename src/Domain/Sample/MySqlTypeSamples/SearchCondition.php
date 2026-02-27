<?php
declare(strict_types=1);

namespace App\Domain\Sample\MySqlTypeSamples;

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
        private readonly ?\DateTimeInterface $dateColFrom,
        private readonly ?\DateTimeInterface $dateColTo,
        private readonly ?\DateTimeInterface $timeColFrom,
        private readonly ?\DateTimeInterface $timeColTo,
        private readonly ?\DateTimeInterface $datetimeColFrom,
        private readonly ?\DateTimeInterface $datetimeColTo,
        private readonly ?string $keyword
    ) {
        // 処理なし
    }

    public function getId() : ?string 
    {
        return $this->id;
    }

    public function getIntColFrom() : ?int
    {
        return $this->intColFrom;
    }

    public function getIntColTo() : ?int
    {
        return $this->intColTo;
    }

    public function getBigintColFrom() : ?int
    {
        return $this->bigintColFrom;
    }

    public function getBigintColTo() : ?int
    {
        return $this->bigintColTo;
    }

    public function getDecimalColFrom() : ?float
    {
        return $this->decimalColFrom;
    }

    public function getDecimalColTo() : ?float
    {
        return $this->decimalColTo;
    }

    public function getFloatColFrom() : ?float
    {
        return $this->floatColFrom;
    }
    
    public function getFloatColTo() : ?float
    {
        return $this->floatColTo;
    }

    public function getDoubleColFrom() : ?float
    {
        return $this->doubleColFrom;
    }

    public function getDoubleColTo() : ?float
    {
        return $this->doubleColTo;
    }

    public function getDateColFrom() : ?\DateTimeInterface
    {
        return $this->dateColFrom;
    }

    public function getDateColTo() : ?\DateTimeInterface
    {
        return $this->dateColTo;
    }

    public function getTimeColFrom() : ?\DateTimeInterface
    {
        return $this->timeColFrom;
    }
    public function getTimeColTo() : ?\DateTimeInterface
    {
        return $this->timeColTo;
    }

    public function getDatetimeColFrom() : ?\DateTimeInterface
    {
        return $this->datetimeColFrom;
    }

    public function getDatetimeColTo() : ?\DateTimeInterface
    {
        return $this->datetimeColTo;
    }

    public function getKeyword() : ?string
    {
        return $this->keyword;
    }
}