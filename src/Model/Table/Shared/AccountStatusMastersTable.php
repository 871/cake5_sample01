<?php
declare(strict_types=1);

namespace App\Model\Table\Shared;

use App\Model\Entity\Shared\AccountStatusMaster;
use App\Model\Table\Admin\AdminAccountHistoriesTable;
use App\Model\Table\Admin\AdminAccountsTable;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * AccountStatusMasters Model
 *
 * @property \App\Model\Table\Admin\AdminAccountHistoriesTable&\Cake\ORM\Association\HasMany $AdminAccountHistories
 * @property \App\Model\Table\Admin\AdminAccountsTable&\Cake\ORM\Association\HasMany $AdminAccounts
 * @method \App\Model\Entity\Shared\AccountStatusMaster newEmptyEntity()
 * @method \App\Model\Entity\Shared\AccountStatusMaster newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\Shared\AccountStatusMaster> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Shared\AccountStatusMaster get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\Shared\AccountStatusMaster findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\Shared\AccountStatusMaster patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\Shared\AccountStatusMaster> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Shared\AccountStatusMaster|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\Shared\AccountStatusMaster saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\Shared\AccountStatusMaster>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Shared\AccountStatusMaster>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Shared\AccountStatusMaster>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Shared\AccountStatusMaster> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Shared\AccountStatusMaster>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Shared\AccountStatusMaster>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Shared\AccountStatusMaster>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Shared\AccountStatusMaster> deleteManyOrFail(iterable $entities, array $options = [])
 */
class AccountStatusMastersTable extends Table
{
    /**
     * Initialize method
     *
     * @param array<string, mixed> $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setEntityClass(AccountStatusMaster::class);
        $this->setTable('account_status_masters');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->hasMany('AdminAccountHistories', [
            'className' => AdminAccountHistoriesTable::class,
            'foreignKey' => 'account_status_master_id',
        ]);
        $this->hasMany('AdminAccounts', [
            'className' => AdminAccountsTable::class,
            'foreignKey' => 'account_status_master_id',
        ]);
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
            ->scalar('code')
            ->maxLength('code', 50)
            ->requirePresence('code', 'create')
            ->notEmptyString('code')
            ->add('code', 'unique', ['rule' => 'validateUnique', 'provider' => 'table']);

        $validator
            ->scalar('name')
            ->maxLength('name', 100)
            ->requirePresence('name', 'create')
            ->notEmptyString('name');

        $validator
            ->scalar('description')
            ->maxLength('description', 255)
            ->allowEmptyString('description');

        $validator
            ->integer('sort')
            ->notEmptyString('sort');

        $validator
            ->integer('is_active')
            ->notEmptyString('is_active');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->isUnique(['code']), ['errorField' => 'code']);

        return $rules;
    }
}
