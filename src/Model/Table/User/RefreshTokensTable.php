<?php
declare(strict_types=1);

namespace App\Model\Table\User;

use App\Model\Entity\User\RefreshToken;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * RefreshTokens Model
 *
 * @property \Cake\ORM\Association\BelongsTo<\App\Model\Table\User\UserAccountsTable> $UserAccounts
 * @method \App\Model\Entity\User\RefreshToken newEmptyEntity()
 * @method \App\Model\Entity\User\RefreshToken newEntity(array<string, mixed> $data, array<string, mixed> $options = [])
 * @method list<\App\Model\Entity\User\RefreshToken> newEntities(array<int, array<string, mixed>> $data, array<string, mixed> $options = [])
 */
final class RefreshTokensTable extends Table
{
    /**
     * @param array<string, mixed> $config
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setEntityClass(RefreshToken::class);
        $this->setTable('refresh_tokens');
        $this->setPrimaryKey('id');

        $this->belongsTo('UserAccounts', [
            'className' => UserAccountsTable::class,
            'foreignKey' => 'user_account_id',
            'joinType' => 'INNER',
        ]);
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->scalar('id')
            ->maxLength('id', 36)
            ->requirePresence('id', 'create')
            ->notEmptyString('id');

        $validator
            ->integer('user_account_id')
            ->requirePresence('user_account_id', 'create')
            ->notEmptyString('user_account_id');

        $validator
            ->dateTime('expires_at')
            ->requirePresence('expires_at', 'create')
            ->notEmptyDateTime('expires_at');

        return $validator;
    }

    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->existsIn(['user_account_id'], 'UserAccounts'), ['errorField' => 'user_account_id']);

        return $rules;
    }
}
