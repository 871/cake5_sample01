<?php
declare(strict_types=1);

namespace App\Model\Entity\Sample;

use Cake\ORM\Entity;

/**
 * MySqlTypeSample Entity
 *
 * @property string $id
 * @property int|null $int_col
 * @property int|null $bigint_col
 * @property string|null $decimal_col
 * @property float|null $float_col
 * @property float|null $double_col
 * @property \Cake\I18n\Date|null $date_col
 * @property \Cake\I18n\Time|null $time_col
 * @property \Cake\I18n\DateTime|null $datetime_col
 * @property string|null $char_col
 * @property string|null $varchar_col
 * @property string|null $text_col
 * @property string|null $mediumtext_col
 * @property string|null $longtext_col
 * @property array|null $json_col
 * @property string|null $search_text
 */
class MySqlTypeSample extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array<string, bool>
     */
    protected array $_accessible = [
        'int_col' => true,
        'bigint_col' => true,
        'decimal_col' => true,
        'float_col' => true,
        'double_col' => true,
        'date_col' => true,
        'time_col' => true,
        'datetime_col' => true,
        'char_col' => true,
        'varchar_col' => true,
        'text_col' => true,
        'mediumtext_col' => true,
        'longtext_col' => true,
        'json_col' => true,
        'search_text' => true,
    ];
}
