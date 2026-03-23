<?php
declare(strict_types=1);

namespace App\Model\Table\Log;

use App\Domain\Log\LoginLogs\ValueObject\LoginActorType;
use App\Model\Entity\Log\LoginLog;
use App\Model\Table\Admin\AdminAccountsTable;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * LoginLogs Model
 *
 * @method \App\Model\Entity\Log\LoginLog newEmptyEntity()
 * @method \App\Model\Entity\Log\LoginLog newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\Log\LoginLog> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Log\LoginLog get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\Log\LoginLog findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\Log\LoginLog patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\Log\LoginLog> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Log\LoginLog|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\Log\LoginLog saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\Log\LoginLog>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Log\LoginLog>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Log\LoginLog>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Log\LoginLog> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Log\LoginLog>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Log\LoginLog>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Log\LoginLog>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Log\LoginLog> deleteManyOrFail(iterable $entities, array $options = [])
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class LoginLogsTable extends Table
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

        $this->setEntityClass(LoginLog::class);
        $this->setTable('login_logs');
        $this->setDisplayField('login_id');
        $this->setPrimaryKey('id');

        /* TODO 未実装
        $this->belongsTo('UserAccounts', [
            'className' => UserAccountsTable::class,
            'foreignKey' => 'account_id',
            'joinType' => 'LEFT',
            'conditions' => [
                'LoginLogs.login_actor_type' => LoginActorType::USER,
            ],
        ]);
        */

        $this->belongsTo('AdminAccounts', [
            'className' => AdminAccountsTable::class,
            'foreignKey' => 'account_id',
            'joinType' => 'LEFT',
            'conditions' => [
                'LoginLogs.login_actor_type' => LoginActorType::ADMIN,
            ],
        ]);

        $this->belongsTo('ImpersonatorAdminAccounts', [
            'className' => AdminAccountsTable::class,
            'foreignKey' => 'impersonator_account_id',
            'joinType' => 'LEFT',
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
            ->scalar('login_id')
            ->maxLength('login_id', 255)
            ->requirePresence('login_id', 'create')
            ->notEmptyString('login_id');

        $validator
            ->scalar('login_actor_type')
            ->maxLength('login_actor_type', 20)
            ->requirePresence('login_actor_type', 'create')
            ->notEmptyString('login_actor_type');

        $validator
            ->allowEmptyString('account_id');

        $validator
            ->allowEmptyString('impersonator_account_id');

        $validator
            ->scalar('login_result')
            ->maxLength('login_result', 20)
            ->requirePresence('login_result', 'create')
            ->notEmptyString('login_result');

        $validator
            ->scalar('ip_address')
            ->maxLength('ip_address', 45)
            ->requirePresence('ip_address', 'create')
            ->notEmptyString('ip_address');

        $validator
            ->scalar('user_agent')
            ->allowEmptyString('user_agent');

        $validator
            ->scalar('failure_reason_code')
            ->maxLength('failure_reason_code', 255)
            ->allowEmptyString('failure_reason_code');

        $validator
            ->dateTime('logged_in_at')
            ->requirePresence('logged_in_at', 'create')
            ->notEmptyDateTime('logged_in_at');

        return $validator;
    }
}
