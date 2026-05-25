<?php
declare(strict_types=1);

namespace App\Model\Table\User;

use App\Model\Entity\User\UserAccount;
use App\Model\Table\Shared\AccountStatusMastersTable;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * UserAccounts Model
 *
 * @property \Cake\ORM\Association\BelongsTo<\App\Model\Table\Shared\AccountStatusMastersTable> $AccountStatusMasters
 * @property \Cake\ORM\Association\HasMany<\App\Model\Table\User\UserAccountHistoriesTable> $UserAccountHistories
 * @property \Cake\ORM\Association\HasMany<\App\Model\Table\User\RefreshTokensTable> $RefreshTokens
 * @method \App\Model\Entity\User\UserAccount newEmptyEntity()
 * @method \App\Model\Entity\User\UserAccount newEntity(array<string, mixed> $data, array<string, mixed> $options = [])
 * @method list<\App\Model\Entity\User\UserAccount> newEntities(array<int, array<string, mixed>> $data, array<string, mixed> $options = [])
 */
final class UserAccountsTable extends Table
{
    /**
     * @param array<string, mixed> $config
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setEntityClass(UserAccount::class);
        $this->setTable('user_accounts');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->belongsTo('AccountStatusMasters', [
            'className' => AccountStatusMastersTable::class,
            'foreignKey' => 'account_status_master_id',
            'joinType' => 'INNER',
        ]);
        $this->hasMany('UserAccountHistories', [
            'className' => UserAccountHistoriesTable::class,
            'foreignKey' => 'user_account_id',
        ]);
        $this->hasMany('RefreshTokens', [
            'className' => RefreshTokensTable::class,
            'foreignKey' => 'user_account_id',
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
