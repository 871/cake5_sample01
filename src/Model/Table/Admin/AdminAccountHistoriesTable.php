<?php
declare(strict_types=1);

namespace App\Model\Table\Admin;

use App\Model\Entity\Admin\AdminAccountHistory;
use App\Model\Table\TableLocatorTrait;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class AdminAccountHistoriesTable extends Table
{
    use TableLocatorTrait;

    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setEntityClass(AdminAccountHistory::class);
        $this->setTable('admin_account_histories');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('AdminAccounts', [
            'className' => AdminAccountsTable::class,
            'foreignKey' => 'admin_account_id',
        ]);
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->scalar('admin_account_id')
            ->requirePresence('admin_account_id', 'create')
            ->notEmptyString('admin_account_id');

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
            ->scalar('role_id')
            ->requirePresence('role_id', 'create')
            ->notEmptyString('role_id');

        $validator
            ->integer('status')
            ->requirePresence('status', 'create')
            ->notEmptyString('status');

        $validator
            ->dateTime('operated_at')
            ->requirePresence('operated_at', 'create')
            ->notEmptyDateTime('operated_at');

        return $validator;
    }
}
