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
        private ?string $id,
        private ?string $int_col,
        private ?string $bigint_col,
        private ?string $decimal_col,
        private ?string $float_col,
        private ?string $double_col,
        private ?string $date_col,
        private ?string $time_col,
        private ?string $datetime_col,
        private ?string $char_col,
        private ?string $varchar_col,
        private ?string $text_col,
        private ?string $mediumtext_col,
        private ?string $longtext_col,
        private ?string $json_col,
    ) {
    }

    /**
     * @return \App\Domain\Sample\MySqlTypeSamples\ValueObject\Id
     */
    public function id(): ValueObject\Id
    {
        return new ValueObject\Id($this->id);
    }

    /**
     * @return \App\Domain\Sample\MySqlTypeSamples\ValueObject\IntCol
     */
    public function intCol(): ValueObject\IntCol
    {
        return new ValueObject\IntCol($this->int_col === null ? null : (int)$this->int_col);
    }

    /**
     * @return \App\Domain\Sample\MySqlTypeSamples\ValueObject\BigintCol
     */
    public function bigintCol(): ValueObject\BigintCol
    {
        return new ValueObject\BigintCol($this->bigint_col === null ? null : (int)$this->bigint_col);
    }

    /**
     * @return \App\Domain\Sample\MySqlTypeSamples\ValueObject\DecimalCol
     */
    public function decimalCol(): ValueObject\DecimalCol
    {
        return new ValueObject\DecimalCol($this->decimal_col);
    }

    /**
     * @return \App\Domain\Sample\MySqlTypeSamples\ValueObject\FloatCol
     */
    public function floatCol(): ValueObject\FloatCol
    {
        return new ValueObject\FloatCol($this->float_col);
    }

    /**
     * @return \App\Domain\Sample\MySqlTypeSamples\ValueObject\DoubleCol
     */
    public function doubleCol(): ValueObject\DoubleCol
    {
        return new ValueObject\DoubleCol($this->double_col);
    }

    /**
     * @return \App\Domain\Sample\MySqlTypeSamples\ValueObject\DateCol
     */
    public function dateCol(): ValueObject\DateCol
    {
        return new ValueObject\DateCol($this->date_col);
    }

    /**
     * @return \App\Domain\Sample\MySqlTypeSamples\ValueObject\TimeCol
     */
    public function timeCol(): ValueObject\TimeCol
    {
        return new ValueObject\TimeCol($this->time_col);
    }

    /**
     * @return \App\Domain\Sample\MySqlTypeSamples\ValueObject\DatetimeCol
     */
    public function datetimeCol(): ValueObject\DatetimeCol
    {
        return new ValueObject\DatetimeCol($this->datetime_col);
    }

    /**
     * @return \App\Domain\Sample\MySqlTypeSamples\ValueObject\CharCol
     */
    public function charCol(): ValueObject\CharCol
    {
        return new ValueObject\CharCol($this->char_col);
    }

    /**
     * @return \App\Domain\Sample\MySqlTypeSamples\ValueObject\VarcharCol
     */
    public function varcharCol(): ValueObject\VarcharCol
    {
        return new ValueObject\VarcharCol($this->varchar_col);
    }

    /**
     * @return \App\Domain\Sample\MySqlTypeSamples\ValueObject\TextCol
     */
    public function textCol(): ValueObject\TextCol
    {
        return new ValueObject\TextCol($this->text_col);
    }

    /**
     * @return \App\Domain\Sample\MySqlTypeSamples\ValueObject\MediumtextCol
     */
    public function mediumtextCol(): ValueObject\MediumtextCol
    {
        return new ValueObject\MediumtextCol($this->mediumtext_col);
    }

    /**
     * @return \App\Domain\Sample\MySqlTypeSamples\ValueObject\LongtextCol
     */
    public function longtextCol(): ValueObject\LongtextCol
    {
        return new ValueObject\LongtextCol($this->longtext_col);
    }

    /**
     * @return \App\Domain\Sample\MySqlTypeSamples\ValueObject\JsonCol
     */
    public function jsonCol(): ValueObject\JsonCol
    {
        return new ValueObject\JsonCol($this->json_col);
    }
}
