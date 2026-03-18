<?php
declare(strict_types=1);

namespace App\Model\Table\Admin;

use App\Model\Entity\Admin\AdminAccount;
use App\Model\Table\Admin\AdminAccountHistoriesTable;
use App\Model\Table\Shared\AccountStatusMastersTable;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * AdminAccounts Model
 *
 * @property \Cake\ORM\Association\BelongsTo<\App\Model\Table\Shared\AccountStatusMastersTable> $AccountStatusMasters
 * @property \Cake\ORM\Association\HasMany<\App\Model\Table\Admin\AdminAccountHistoriesTable> $AdminAccountHistories
 *
 * @method AdminAccount newEmptyEntity()
 * @method AdminAccount newEntity(array<string, mixed> $data, array<string, mixed> $options = [])
 * @method list<AdminAccount> newEntities(array<int, array<string, mixed>> $data, array<string, mixed> $options = [])
 */
final class AdminAccountsTable extends Table
{
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
        ]);
    }

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