<?php
declare(strict_types=1);

namespace App\Model\Table\Grant;

use App\Model\Entity\Grant\GrantRolePermission;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * GrantRolePermissions Model
 *
 * @property \Cake\ORM\Association\BelongsTo<\App\Model\Table\Grant\GrantRolesTable> $GrantRoles
 * @property \Cake\ORM\Association\BelongsTo<\App\Model\Table\Grant\GrantPermissionsTable> $GrantPermissions
 * @method \App\Model\Entity\Grant\GrantRolePermission newEmptyEntity()
 * @method \App\Model\Entity\Grant\GrantRolePermission newEntity(array<string, mixed> $data, array<string, mixed> $options = [])
 * @method list<\App\Model\Entity\Grant\GrantRolePermission> newEntities(array<int, array<string, mixed>> $data, array<string, mixed> $options = [])
 */
final class GrantRolePermissionsTable extends Table
{
    /**
     * @param array<string, mixed> $config
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setEntityClass(GrantRolePermission::class);
        $this->setTable('grant_role_permissions');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('GrantRoles', [
            'className' => GrantRolesTable::class,
            'foreignKey' => 'grant_role_id',
            'joinType' => 'INNER',
        ]);

        $this->belongsTo('GrantPermissions', [
            'className' => GrantPermissionsTable::class,
            'foreignKey' => 'grant_permission_id',
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
            ->integer('grant_role_id')
            ->requirePresence('grant_role_id', 'create')
            ->notEmptyString('grant_role_id');

        $validator
            ->integer('grant_permission_id')
            ->requirePresence('grant_permission_id', 'create')
            ->notEmptyString('grant_permission_id');

        return $validator;
    }
}
