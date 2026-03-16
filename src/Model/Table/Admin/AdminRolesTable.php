<?php
declare(strict_types=1);

namespace App\Model\Table\Admin;

use App\Model\Entity\Admin\AdminRole;
use App\Model\Table\TableLocatorTrait;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class AdminRolesTable extends Table
{
    use TableLocatorTrait;

    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setEntityClass(AdminRole::class);
        $this->setTable('admin_roles');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->scalar('name')
            ->maxLength('name', 100)
            ->requirePresence('name', 'create')
            ->notEmptyString('name');

        return $validator;
    }
}
