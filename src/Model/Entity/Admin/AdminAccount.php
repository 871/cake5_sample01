<?php
declare(strict_types=1);

namespace App\Model\Entity\Admin;

use Cake\ORM\Entity;

/**
 * AdminAccount Entity
 *
 * @property string $id
 * @property string $login_id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string $role_id
 * @property int $status
 * @property \App\Model\Entity\Admin\AdminRole $admin_role
 * @property \App\Model\Entity\Admin\AdminAccountHistory[] $admin_account_histories
 */
class AdminAccount extends Entity
{
    protected array $_accessible = [
        'login_id' => true,
        'name' => true,
        'email' => true,
        'password' => true,
        'role_id' => true,
        'status' => true,
        'admin_role' => true,
        'admin_account_histories' => true,
    ];

    protected array $_hidden = [
        'password',
    ];
}
