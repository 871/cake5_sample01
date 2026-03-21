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
 * @property \Cake\ORM\Association\HasMany<\App\Model\Table\Admin\AdminAccountsTable> $AdminAccounts
 * @property \Cake\ORM\Association\HasMany<\App\Model\Table\Admin\AdminAccountHistoriesTable> $AdminAccountHistories
 * @method \App\Model\Entity\Shared\AccountStatusMaster newEmptyEntity()
 * @method \App\Model\Entity\Shared\AccountStatusMaster newEntity(array<string, mixed> $data, array<string, mixed> $options = [])
 * @method list<\App\Model\Entity\Shared\AccountStatusMaster> newEntities(array<int, array<string, mixed>> $data, array<string, mixed> $options = [])
 */
final class AccountStatusMastersTable extends Table
{
    /**
     * @param array<string, mixed> $config
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setEntityClass(AccountStatusMaster::class);
        $this->setTable('account_status_masters');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->hasMany('AdminAccounts', [
            'className' => AdminAccountsTable::class,
            'foreignKey' => 'account_status_master_id',
        ]);
        $this->hasMany('AdminAccountHistories', [
            'className' => AdminAccountHistoriesTable::class,
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
            ->add('code', 'unique', [
                'rule' => 'validateUnique',
                'provider' => 'table',
            ]);

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
            ->boolean('is_active')
            ->notEmptyString('is_active');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating application integrity.
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
