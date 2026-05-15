<?php
declare(strict_types=1);

namespace App\Model\Table\Grant;

use App\Model\Entity\Grant\GrantAccountRole;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * GrantAccountRoles Model
 *
 * @property \Cake\ORM\Association\BelongsTo<\App\Model\Table\Grant\GrantRolesTable> $GrantRoles
 * @method \App\Model\Entity\Grant\GrantAccountRole newEmptyEntity()
 * @method \App\Model\Entity\Grant\GrantAccountRole newEntity(array<string, mixed> $data, array<string, mixed> $options = [])
 * @method list<\App\Model\Entity\Grant\GrantAccountRole> newEntities(array<int, array<string, mixed>> $data, array<string, mixed> $options = [])
 */
final class GrantAccountRolesTable extends Table
{
    /**
     * @param array<string, mixed> $config
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setEntityClass(GrantAccountRole::class);
        $this->setTable('grant_account_roles');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('GrantRoles', [
            'className' => GrantRolesTable::class,
            'foreignKey' => 'grant_role_id',
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
            ->scalar('id')
            ->maxLength('id', 36)
            ->requirePresence('id', 'create')
            ->notEmptyString('id');

        $validator
            ->scalar('account_type')
            ->maxLength('account_type', 20)
            ->requirePresence('account_type', 'create')
            ->notEmptyString('account_type');

        $validator
            ->integer('account_id')
            ->requirePresence('account_id', 'create')
            ->notEmptyString('account_id');

        $validator
            ->integer('grant_role_id')
            ->requirePresence('grant_role_id', 'create')
            ->notEmptyString('grant_role_id');

        return $validator;
    }
}
