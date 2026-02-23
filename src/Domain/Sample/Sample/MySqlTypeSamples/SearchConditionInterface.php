<?php
declare(strict_types=1);

namespace App\Domain\Sample\Sample\MySqlTypeSamples;

interface SearchConditionInterface
{
    public function getId() : ?string;

    public function getIntColFrom() : ?int;

    public function getIntColTo() : ?int;

    public function getBigintColFrom() : ?int;

    public function getBigintColTo() : ?int;

    public function getDecimalColFrom() : ?float;

    public function getDecimalColTo() : ?float;

    public function getFloatColFrom() : ?float;
    
    public function getFloatColTo() : ?float;

    public function getDoubleColFrom() : ?float;

    public function getDoubleColTo() : ?float;

    public function getDateColFrom() : ?\DateTimeInterface;

    public function getDateColTo() : ?\DateTimeInterface;

    public function getTimeColFrom() : ?\DateTimeInterface;

    public function getTimeColTo() : ?\DateTimeInterface;

    public function getDatetimeColFrom() : ?\DateTimeInterface;

    public function getDatetimeColTo() : ?\DateTimeInterface;

    public function getKeyword() : ?string;
}