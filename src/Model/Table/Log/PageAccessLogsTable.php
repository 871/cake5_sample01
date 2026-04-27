<?php
declare(strict_types=1);

namespace App\Model\Table\Log;

use App\Model\Entity\Log\PageAccessLog;
use App\Model\Table\Admin\AdminAccountsTable;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * PageAccessLogs Model
 *
 * @method \App\Model\Entity\Log\PageAccessLog newEmptyEntity()
 * @method \App\Model\Entity\Log\PageAccessLog newEntity(array<string, mixed> $data, array<string, mixed> $options = [])
 * @method array<\App\Model\Entity\Log\PageAccessLog> newEntities(array<int, array<string, mixed>> $data, array<string, mixed> $options = [])
 * @method \App\Model\Entity\Log\PageAccessLog get(mixed $primaryKey, array<string, mixed>|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\Log\PageAccessLog findOrCreate(array<string, mixed> $search, ?callable $callback = null, array<string, mixed> $options = [])
 * @method \App\Model\Entity\Log\PageAccessLog patchEntity(\Cake\Datasource\EntityInterface $entity, array<string, mixed> $data, array<string, mixed> $options = [])
 * @method array<\App\Model\Entity\Log\PageAccessLog> patchEntities(iterable<\App\Model\Entity\Log\PageAccessLog> $entities, array<int, array<string, mixed>> $data, array<string, mixed> $options = [])
 * @method \App\Model\Entity\Log\PageAccessLog|false save(\Cake\Datasource\EntityInterface $entity, array<string, mixed> $options = [])
 * @method \App\Model\Entity\Log\PageAccessLog saveOrFail(\Cake\Datasource\EntityInterface $entity, array<string, mixed> $options = [])
 * @method iterable<\App\Model\Entity\Log\PageAccessLog>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Log\PageAccessLog>|false saveMany(iterable<\App\Model\Entity\Log\PageAccessLog> $entities, array<string, mixed> $options = [])
 * @method iterable<\App\Model\Entity\Log\PageAccessLog>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Log\PageAccessLog> saveManyOrFail(iterable<\App\Model\Entity\Log\PageAccessLog> $entities, array<string, mixed> $options = [])
 * @method iterable<\App\Model\Entity\Log\PageAccessLog>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Log\PageAccessLog>|false deleteMany(iterable<\App\Model\Entity\Log\PageAccessLog> $entities, array<string, mixed> $options = [])
 * @method iterable<\App\Model\Entity\Log\PageAccessLog>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Log\PageAccessLog> deleteManyOrFail(iterable<\App\Model\Entity\Log\PageAccessLog> $entities, array<string, mixed> $options = [])
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class PageAccessLogsTable extends Table
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

        $this->setEntityClass(PageAccessLog::class);
        $this->setTable('page_access_logs');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('AdminAccounts', [
            'className' => AdminAccountsTable::class,
            'foreignKey' => 'account_id',
            'joinType' => 'LEFT',
            'conditions' => [
                'PageAccessLogs.account_type' => 'ADMIN',
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
            ->dateTime('accessed')
            ->requirePresence('accessed', 'create')
            ->notEmptyDateTime('accessed');

        $validator
            ->scalar('account_type')
            ->maxLength('account_type', 20)
            ->requirePresence('account_type', 'create')
            ->notEmptyString('account_type');

        $validator
            ->integer('account_id')
            ->allowEmptyString('account_id');

        $validator
            ->scalar('method')
            ->maxLength('method', 10)
            ->requirePresence('method', 'create')
            ->notEmptyString('method');

        $validator
            ->scalar('path')
            ->maxLength('path', 2048)
            ->requirePresence('path', 'create')
            ->notEmptyString('path');

        $validator
            ->scalar('query_string')
            ->allowEmptyString('query_string');

        $validator
            ->scalar('post_keys')
            ->allowEmptyString('post_keys');

        $validator
            ->scalar('route_name')
            ->maxLength('route_name', 2048)
            ->allowEmptyString('route_name');

        $validator
            ->scalar('referer')
            ->allowEmptyString('referer');

        $validator
            ->scalar('ip_address')
            ->maxLength('ip_address', 45)
            ->allowEmptyString('ip_address');

        $validator
            ->scalar('user_agent')
            ->allowEmptyString('user_agent');

        return $validator;
    }
}
