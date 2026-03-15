<?php
declare(strict_types=1);

namespace App\Model\Table\Admin;

use App\Model\Entity\Admin\AdminAccountHistory;
use App\Model\Table\Admin\AdminAccountsTable;
use App\Model\Table\Shared\AccountStatusMastersTable;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * AdminAccountHistories Model
 *
 * @property \App\Model\Table\Admin\AdminAccountsTable&\Cake\ORM\Association\BelongsTo $AdminAccounts
 * @property \App\Model\Table\Shared\AccountStatusMastersTable&\Cake\ORM\Association\BelongsTo $AccountStatusMasters
 *
 * @method \App\Model\Entity\Admin\AdminAccountHistory newEmptyEntity()
 * @method \App\Model\Entity\Admin\AdminAccountHistory newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\Admin\AdminAccountHistory> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Admin\AdminAccountHistory get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\Admin\AdminAccountHistory findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\Admin\AdminAccountHistory patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\Admin\AdminAccountHistory> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Admin\AdminAccountHistory|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\Admin\AdminAccountHistory saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\Admin\AdminAccountHistory>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Admin\AdminAccountHistory>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Admin\AdminAccountHistory>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Admin\AdminAccountHistory> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Admin\AdminAccountHistory>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Admin\AdminAccountHistory>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Admin\AdminAccountHistory>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Admin\AdminAccountHistory> deleteManyOrFail(iterable $entities, array $options = [])
 *
 */
class AdminAccountHistoriesTable extends Table
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

        $this->setEntityClass(AdminAccountHistory::class);
        $this->setTable('admin_account_histories');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->belongsTo('AdminAccounts', [
            'className' => AdminAccountsTable::class,
            'foreignKey' => 'admin_account_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('AccountStatusMasters', [
            'className' => AccountStatusMastersTable::class,
            'foreignKey' => 'account_status_master_id',
            'joinType' => 'INNER',
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
            ->notEmptyString('admin_account_id');

        $validator
            ->email('email')
            ->requirePresence('email', 'create')
            ->notEmptyString('email');

        $validator
            ->scalar('password')
            ->maxLength('password', 255)
            ->requirePresence('password', 'create')
            ->notEmptyString('password');

        $validator
            ->scalar('name')
            ->maxLength('name', 100)
            ->requirePresence('name', 'create')
            ->notEmptyString('name');

        $validator
            ->scalar('admin_note')
            ->allowEmptyString('admin_note');

        $validator
            ->integer('account_status_master_id')
            ->notEmptyString('account_status_master_id');

        $validator
            ->integer('is_email_verified')
            ->notEmptyString('is_email_verified');

        $validator
            ->dateTime('password_changed_at')
            ->requirePresence('password_changed_at', 'create')
            ->notEmptyDateTime('password_changed_at');

        $validator
            ->dateTime('password_expires_at')
            ->requirePresence('password_expires_at', 'create')
            ->notEmptyDateTime('password_expires_at');

        $validator
            ->allowEmptyString('created_by');

        $validator
            ->scalar('created_ip')
            ->maxLength('created_ip', 45)
            ->allowEmptyString('created_ip');

        $validator
            ->allowEmptyString('modified_by');

        $validator
            ->scalar('modified_ip')
            ->maxLength('modified_ip', 45)
            ->allowEmptyString('modified_ip');

        $validator
            ->scalar('operation_type')
            ->maxLength('operation_type', 10)
            ->requirePresence('operation_type', 'create')
            ->notEmptyString('operation_type');

        $validator
            ->dateTime('history_created')
            ->requirePresence('history_created', 'create')
            ->notEmptyDateTime('history_created');

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
        $rules->add($rules->existsIn(['admin_account_id'], 'AdminAccounts'), ['errorField' => 'admin_account_id']);
        $rules->add($rules->existsIn(['account_status_master_id'], 'AccountStatusMasters'), ['errorField' => 'account_status_master_id']);

        return $rules;
    }
}
