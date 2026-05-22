<?php
declare(strict_types=1);

namespace App\Model\Table\Admin;

use App\Model\Entity\Admin\AdminAccount;
use App\Model\Table\Grant\GrantAccountPermissionsTable;
use App\Model\Table\Grant\GrantAccountRolesTable;
use App\Model\Table\Shared\AccountStatusMastersTable;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * AdminAccounts Model
 *
 * @property \Cake\ORM\Association\BelongsTo<\App\Model\Table\Shared\AccountStatusMastersTable> $AccountStatusMasters
 * @property \Cake\ORM\Association\HasMany<\App\Model\Table\Admin\AdminAccountHistoriesTable> $AdminAccountHistories
 * @method \App\Model\Entity\Admin\AdminAccount newEmptyEntity()
 * @method \App\Model\Entity\Admin\AdminAccount newEntity(array<string, mixed> $data, array<string, mixed> $options = [])
 * @method list<\App\Model\Entity\Admin\AdminAccount> newEntities(array<int, array<string, mixed>> $data, array<string, mixed> $options = [])
 */
final class AdminAccountsTable extends Table
{
    /**
     * @param array<string, mixed> $config
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setEntityClass(AdminAccount::class);
        $this->setTable('admin_accounts');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->belongsTo('AccountStatusMasters', [
            'className' => AccountStatusMastersTable::class,
            'foreignKey' => 'account_status_master_id',
            'joinType' => 'INNER',
        ]);

        $this->hasMany('AdminAccountHistories', [
            'className' => AdminAccountHistoriesTable::class,
            'foreignKey' => 'admin_account_id',
            'sort' => [
                'AdminAccountHistories.created' => 'DESC',
            ],
        ]);

        $this->hasMany('GrantAccountPermissions', [
            'className' => GrantAccountPermissionsTable::class,
            'foreignKey' => 'account_id',
            'conditions' => [
            ],
            'sort' => [
                'GrantAccountPermissions.created' => 'DESC',
            ],
        ]);

        $this->hasMany('GrantAccountRoles', [
            'className' => GrantAccountRolesTable::class,
            'foreignKey' => 'account_id',
            'conditions' => [
            ],
            'sort' => [
                'GrantAccountRoles.created' => 'DESC',
            ],
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
            ->email('email')
            ->requirePresence('email', 'create')
            ->notEmptyString('email')
            ->add('email', 'unique', [
                'rule' => 'validateUnique',
                'provider' => 'table',
            ]);

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
            ->boolean('is_email_verified')
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
        $rules->add($rules->isUnique(['email']), ['errorField' => 'email']);

        $rules->add(
            $rules->existsIn(['account_status_master_id'], 'AccountStatusMasters'),
            ['errorField' => 'account_status_master_id'],
        );

        return $rules;
    }
}
