<?php
declare(strict_types=1);

namespace App\Model\Table\Admin;

use App\Model\Entity\Admin\AdminAccount;
use App\Model\Table\TableLocatorTrait;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class AdminAccountsTable extends Table
{
    use TableLocatorTrait;

    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setEntityClass(AdminAccount::class);
        $this->setTable('admin_accounts');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->belongsTo('AdminRoles', [
            'className' => AdminRolesTable::class,
            'foreignKey' => 'role_id',
        ]);

        $this->hasMany('AdminAccountHistories', [
            'className' => AdminAccountHistoriesTable::class,
            'foreignKey' => 'admin_account_id',
            'dependent' => true,
        ]);
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->scalar('login_id')
            ->maxLength('login_id', 100)
            ->requirePresence('login_id', 'create')
            ->notEmptyString('login_id');

        $validator
            ->scalar('name')
            ->maxLength('name', 100)
            ->requirePresence('name', 'create')
            ->notEmptyString('name');

        $validator
            ->email('email')
            ->maxLength('email', 255)
            ->requirePresence('email', 'create')
            ->notEmptyString('email');

        $validator
            ->scalar('password')
            ->maxLength('password', 255)
            ->requirePresence('password', 'create')
            ->notEmptyString('password');

        $validator
            ->scalar('role_id')
            ->requirePresence('role_id', 'create')
            ->notEmptyString('role_id');

        $validator
            ->integer('status')
            ->requirePresence('status', 'create')
            ->notEmptyString('status');

        return $validator;
    }
}
