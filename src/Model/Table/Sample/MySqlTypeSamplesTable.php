<?php
declare(strict_types=1);

namespace App\Model\Table\Sample;

use App\Model\Entity\Sample\MySqlTypeSample;
use App\Model\Table\TableLocatorTrait;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * MySqlTypeSamples Model
 *
 * @method MySqlTypeSample newEmptyEntity()
 * @method MySqlTypeSample newEntity(array<string, mixed> $data, array<string, mixed> $options = [])
 * @method list<MySqlTypeSample> newEntities(array<int, array<string, mixed>> $data, array<string, mixed> $options = [])
 */
final class MySqlTypeSamplesTable extends Table
{
    use TableLocatorTrait;

    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setEntityClass(MySqlTypeSample::class);
        $this->setTable('my_sql_type_samples');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('int_col')
            ->allowEmptyString('int_col');

        $validator
            ->allowEmptyString('bigint_col');

        $validator
            ->decimal('decimal_col')
            ->allowEmptyString('decimal_col');

        $validator
            ->numeric('float_col')
            ->allowEmptyString('float_col');

        $validator
            ->numeric('double_col')
            ->allowEmptyString('double_col');

        $validator
            ->date('date_col')
            ->allowEmptyDate('date_col');

        $validator
            ->time('time_col')
            ->allowEmptyTime('time_col');

        $validator
            ->dateTime('datetime_col')
            ->allowEmptyDateTime('datetime_col');

        $validator
            ->scalar('char_col')
            ->maxLength('char_col', 10)
            ->allowEmptyString('char_col');

        $validator
            ->scalar('varchar_col')
            ->maxLength('varchar_col', 255)
            ->allowEmptyString('varchar_col');

        $validator
            ->scalar('text_col')
            ->allowEmptyString('text_col');

        $validator
            ->scalar('mediumtext_col')
            ->maxLength('mediumtext_col', 16777215)
            ->allowEmptyString('mediumtext_col');

        $validator
            ->scalar('longtext_col')
            ->maxLength('longtext_col', 4294967295)
            ->allowEmptyString('longtext_col');

        $validator
            ->allowEmptyString('json_col');

        $validator
            ->scalar('search_text')
            ->maxLength('search_text', 4294967295)
            ->allowEmptyString('search_text');

        return $validator;
    }
}
