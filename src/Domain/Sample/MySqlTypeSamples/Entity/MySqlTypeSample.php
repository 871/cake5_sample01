<?php
declare(strict_types=1);

namespace App\Domain\Sample\MySqlTypeSamples\Entity;

use App\Domain\Sample\MySqlTypeSamples\ValueObject;

final class MySqlTypeSample
{
    /**
     * @param ?string $id
     * @param ?string $int_col
     * @param ?string $bigint_col
     * @param ?string $decimal_col
     * @param ?string $float_col
     * @param ?string $double_col
     * @param ?string $date_col
     * @param ?string $time_col
     * @param ?string $datetime_col
     * @param ?string $char_col
     * @param ?string $varchar_col
     * @param ?string $text_col
     * @param ?string $mediumtext_col
     * @param ?string $longtext_col
     * @param ?string $json_col
     */
    public function __construct(
        private readonly ?string $id,
        private readonly ?string $int_col,
        private readonly ?string $bigint_col,
        private readonly ?string $decimal_col,
        private readonly ?string $float_col,
        private readonly ?string $double_col,
        private readonly ?string $date_col,
        private readonly ?string $time_col,
        private readonly ?string $datetime_col,
        private readonly ?string $char_col,
        private readonly ?string $varchar_col,
        private readonly ?string $text_col,
        private readonly ?string $mediumtext_col,
        private readonly ?string $longtext_col,
        private readonly ?string $json_col,
    ) {
    }

    /**
     * @return \App\Domain\Sample\MySqlTypeSamples\ValueObject\Id
     */
    public function id(): ValueObject\Id
    {
        return ValueObject\Id::fromString($this->id);
    }

    /**
     * @return \App\Domain\Sample\MySqlTypeSamples\ValueObject\IntCol
     */
    public function intCol(): ValueObject\IntCol
    {
        return ValueObject\IntCol::fromString($this->int_col);
    }

    /**
     * @return \App\Domain\Sample\MySqlTypeSamples\ValueObject\BigintCol
     */
    public function bigintCol(): ValueObject\BigintCol
    {
        return ValueObject\BigintCol::fromString($this->bigint_col);
    }

    /**
     * @return \App\Domain\Sample\MySqlTypeSamples\ValueObject\DecimalCol
     */
    public function decimalCol(): ValueObject\DecimalCol
    {
        return ValueObject\DecimalCol::fromString($this->decimal_col);
    }

    /**
     * @return \App\Domain\Sample\MySqlTypeSamples\ValueObject\FloatCol
     */
    public function floatCol(): ValueObject\FloatCol
    {
        return ValueObject\FloatCol::fromString($this->float_col);
    }

    /**
     * @return \App\Domain\Sample\MySqlTypeSamples\ValueObject\DoubleCol
     */
    public function doubleCol(): ValueObject\DoubleCol
    {
        return ValueObject\DoubleCol::fromString($this->double_col);
    }

    /**
     * @return \App\Domain\Sample\MySqlTypeSamples\ValueObject\DateCol
     */
    public function dateCol(): ValueObject\DateCol
    {
        return ValueObject\DateCol::fromString($this->date_col);
    }

    /**
     * @return \App\Domain\Sample\MySqlTypeSamples\ValueObject\TimeCol
     */
    public function timeCol(): ValueObject\TimeCol
    {
        return ValueObject\TimeCol::fromString($this->time_col);
    }

    /**
     * @return \App\Domain\Sample\MySqlTypeSamples\ValueObject\DatetimeCol
     */
    public function datetimeCol(): ValueObject\DatetimeCol
    {
        return ValueObject\DatetimeCol::fromString($this->datetime_col);
    }

    /**
     * @return \App\Domain\Sample\MySqlTypeSamples\ValueObject\CharCol
     */
    public function charCol(): ValueObject\CharCol
    {
        return ValueObject\CharCol::fromString($this->char_col);
    }

    /**
     * @return \App\Domain\Sample\MySqlTypeSamples\ValueObject\VarcharCol
     */
    public function varcharCol(): ValueObject\VarcharCol
    {
        return ValueObject\VarcharCol::fromString($this->varchar_col);
    }

    /**
     * @return \App\Domain\Sample\MySqlTypeSamples\ValueObject\TextCol
     */
    public function textCol(): ValueObject\TextCol
    {
        return ValueObject\TextCol::fromString($this->text_col);
    }

    /**
     * @return \App\Domain\Sample\MySqlTypeSamples\ValueObject\MediumtextCol
     */
    public function mediumtextCol(): ValueObject\MediumtextCol
    {
        return ValueObject\MediumtextCol::fromString($this->mediumtext_col);
    }

    /**
     * @return \App\Domain\Sample\MySqlTypeSamples\ValueObject\LongtextCol
     */
    public function longtextCol(): ValueObject\LongtextCol
    {
        return ValueObject\LongtextCol::fromString($this->longtext_col);
    }

    /**
     * @return \App\Domain\Sample\MySqlTypeSamples\ValueObject\JsonCol
     */
    public function jsonCol(): ValueObject\JsonCol
    {
        return ValueObject\JsonCol::fromString($this->json_col);
    }
}
