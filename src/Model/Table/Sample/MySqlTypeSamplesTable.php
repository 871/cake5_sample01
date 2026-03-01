<?php
declare(strict_types=1);

namespace App\Model\Table\Sample;

use App\Model\Table\TableLocatorTrait;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * MySqlTypeSamples Model
 *
 * @method \App\Model\Entity\Sample\MySqlTypeSample newEmptyEntity()
 * @method \App\Model\Entity\Sample\MySqlTypeSample newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\Sample\MySqlTypeSample> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Sample\MySqlTypeSample get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\Sample\MySqlTypeSample findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\Sample\MySqlTypeSample patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\Sample\MySqlTypeSample> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Sample\MySqlTypeSample|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\Sample\MySqlTypeSample saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\Sample\MySqlTypeSample>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\MySqlTypeSample>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Sample\MySqlTypeSample>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\MySqlTypeSample> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Sample\MySqlTypeSample>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\MySqlTypeSample>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Sample\MySqlTypeSample>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\MySqlTypeSample> deleteManyOrFail(iterable $entities, array $options = [])
 */
class MySqlTypeSamplesTable extends Table
{
    use TableLocatorTrait;

    /**
     * Initialize method
     *
     * @param array<string, mixed> $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('my_sql_type_samples');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
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
