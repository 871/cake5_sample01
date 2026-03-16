<?php
declare(strict_types=1);

namespace App\Model\Entity\Admin;

use Cake\I18n\DateTime;
use Cake\ORM\Entity;

/**
 * AdminAccountHistory Entity
 *
 * @property string $id
 * @property string $admin_account_id
 * @property string $login_id
 * @property string $name
 * @property string $email
 * @property string $role_id
 * @property int $status
 * @property \Cake\I18n\DateTime $operated_at
 * @property \App\Model\Entity\Admin\AdminAccount $admin_account
 */
class AdminAccountHistory extends Entity
{
    protected array $_accessible = [
        'admin_account_id' => true,
        'login_id' => true,
        'name' => true,
        'email' => true,
        'role_id' => true,
        'status' => true,
        'operated_at' => true,
        'admin_account' => true,
    ];
}
