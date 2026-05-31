<?php
declare(strict_types=1);

namespace App\Model\Table\Grant;

use App\Model\Entity\Grant\GrantAccountPermission;
use App\Model\Table\Admin\AdminAccountsTable;
use App\Model\Table\User\UserAccountsTable;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * GrantAccountPermissions Model
 *
 * @property \Cake\ORM\Association\BelongsTo<\App\Model\Table\Admin\AdminAccountsTable> $AdminAccounts
 * @property \Cake\ORM\Association\BelongsTo<\App\Model\Table\User\UserAccountsTable> $UserAccounts
 * @property \Cake\ORM\Association\BelongsTo<\App\Model\Table\Grant\GrantPermissionsTable> $GrantPermissions
 * @method \App\Model\Entity\Grant\GrantAccountPermission newEmptyEntity()
 * @method \App\Model\Entity\Grant\GrantAccountPermission newEntity(array<string, mixed> $data, array<string, mixed> $options = [])
 * @method list<\App\Model\Entity\Grant\GrantAccountPermission> newEntities(array<int, array<string, mixed>> $data, array<string, mixed> $options = [])
 */
final class GrantAccountPermissionsTable extends Table
{
    /**
     * @param array<string, mixed> $config
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setEntityClass(GrantAccountPermission::class);
        $this->setTable('grant_account_permissions');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('AdminAccounts', [
            'className' => AdminAccountsTable::class,
            'foreignKey' => 'account_id',
            'conditions' => [
                'GrantAccountPermissions.account_type' => 'ADMIN',
            ],
            'joinType' => 'INNER',
        ]);

        $this->belongsTo('UserAccounts', [
            'className' => UserAccountsTable::class,
            'foreignKey' => 'account_id',
            'conditions' => [
                'GrantAccountPermissions.account_type' => 'USER',
            ],
            'joinType' => 'INNER',
        ]);

        $this->belongsTo('GrantPermissions', [
            'className' => GrantPermissionsTable::class,
            'foreignKey' => 'grant_permission_id',
            'conditions' => [
                'GrantPermissions.account_type = GrantAccountPermissions.account_type',
            ],
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
            ->integer('grant_permission_id')
            ->requirePresence('grant_permission_id', 'create')
            ->notEmptyString('grant_permission_id');

        return $validator;
    }
}
