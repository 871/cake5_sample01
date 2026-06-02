<?php
declare(strict_types=1);

namespace App\Model\Table\Grant;

use App\Model\Entity\Grant\GrantRole;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * GrantRoles Model
 *
 * @property \Cake\ORM\Association\HasMany<\App\Model\Table\Grant\GrantAccountRolesTable> $GrantAccountRoles
 * @property \Cake\ORM\Association\HasMany<\App\Model\Table\Grant\GrantRolePermissionsTable> $GrantRolePermissions
 * @method \App\Model\Entity\Grant\GrantRole newEmptyEntity()
 * @method \App\Model\Entity\Grant\GrantRole newEntity(array<string, mixed> $data, array<string, mixed> $options = [])
 * @method list<\App\Model\Entity\Grant\GrantRole> newEntities(array<int, array<string, mixed>> $data, array<string, mixed> $options = [])
 */
final class GrantRolesTable extends Table
{
    /**
     * @param array<string, mixed> $config
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setEntityClass(GrantRole::class);
        $this->setTable('grant_roles');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->hasMany('GrantAccountRoles', [
            'className' => GrantAccountRolesTable::class,
            'foreignKey' => 'grant_role_id',
            'conditions' => [

            ],
            'sort' => [
                'GrantAccountRoles.created' => 'DESC',
            ],
        ]);

        $this->hasMany('GrantRolePermissions', [
            'className' => GrantRolePermissionsTable::class,
            'foreignKey' => 'grant_role_id',
            'conditions' => [

            ],
            'sort' => [
                'GrantRolePermissions.created' => 'DESC',
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
            ->scalar('account_type')
            ->maxLength('account_type', 20)
            ->requirePresence('account_type', 'create')
            ->notEmptyString('account_type');

        $validator
            ->scalar('code')
            ->maxLength('code', 100)
            ->requirePresence('code', 'create')
            ->notEmptyString('code');

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
            ->integer('is_active')
            ->notEmptyString('is_active');

        return $validator;
    }
}
