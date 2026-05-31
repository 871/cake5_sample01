<?php
declare(strict_types=1);

namespace App\Model\Table\Grant;

use App\Model\Entity\Grant\GrantPermission;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * GrantPermissions Model
 *
 * @property \Cake\ORM\Association\HasMany<\App\Model\Table\Grant\GrantRolePermissionsTable> $GrantRolePermissions
 * @property \Cake\ORM\Association\HasMany<\App\Model\Table\Grant\GrantAccountPermissionsTable> $GrantAccountPermissions
 * @method \App\Model\Entity\Grant\GrantPermission newEmptyEntity()
 * @method \App\Model\Entity\Grant\GrantPermission newEntity(array<string, mixed> $data, array<string, mixed> $options = [])
 * @method list<\App\Model\Entity\Grant\GrantPermission> newEntities(array<int, array<string, mixed>> $data, array<string, mixed> $options = [])
 */
final class GrantPermissionsTable extends Table
{
    /**
     * @param array<string, mixed> $config
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setEntityClass(GrantPermission::class);
        $this->setTable('grant_permissions');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->hasMany('GrantRolePermissions', [
            'className' => GrantRolePermissionsTable::class,
            'foreignKey' => 'grant_permission_id',
            'conditions' => [
                'GrantRolePermissions.account_type = GrantPermissions.account_type',
            ],
            'sort' => [
                'GrantRolePermissions.created' => 'DESC',
            ],
        ]);

        $this->hasMany('GrantAccountPermissions', [
            'className' => GrantAccountPermissionsTable::class,
            'foreignKey' => 'grant_permission_id',
            'conditions' => [
                'GrantAccountPermissions.account_type = GrantPermissions.account_type',
            ],
            'sort' => [
                'GrantAccountPermissions.created' => 'DESC',
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
