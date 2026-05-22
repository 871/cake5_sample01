<?php
declare(strict_types=1);

namespace App\Model\Table\User;

use App\Model\Entity\User\UserAccountHistory;
use App\Model\Table\Shared\AccountStatusMastersTable;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * UserAccountHistories Model
 *
 * @property \Cake\ORM\Association\BelongsTo<\App\Model\Table\User\UserAccountsTable> $UserAccounts
 * @property \Cake\ORM\Association\BelongsTo<\App\Model\Table\Shared\AccountStatusMastersTable> $AccountStatusMasters
 * @method \App\Model\Entity\User\UserAccountHistory newEmptyEntity()
 * @method \App\Model\Entity\User\UserAccountHistory newEntity(array<string, mixed> $data, array<string, mixed> $options = [])
 * @method list<\App\Model\Entity\User\UserAccountHistory> newEntities(array<int, array<string, mixed>> $data, array<string, mixed> $options = [])
 */
final class UserAccountHistoriesTable extends Table
{
    /**
     * @param array<string, mixed> $config
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setEntityClass(UserAccountHistory::class);
        $this->setTable('user_account_histories');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->belongsTo('UserAccounts', [
            'className' => UserAccountsTable::class,
            'foreignKey' => 'user_account_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('AccountStatusMasters', [
            'className' => AccountStatusMastersTable::class,
            'foreignKey' => 'account_status_master_id',
            'joinType' => 'INNER',
        ]);
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->notEmptyString('user_account_id');

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

    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->existsIn(['user_account_id'], 'UserAccounts'), ['errorField' => 'user_account_id']);
        $rules->add(
            $rules->existsIn(['account_status_master_id'], 'AccountStatusMasters'),
            ['errorField' => 'account_status_master_id'],
        );

        return $rules;
    }
}
