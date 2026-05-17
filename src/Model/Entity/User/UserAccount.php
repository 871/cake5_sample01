<?php
declare(strict_types=1);

namespace App\Model\Entity\User;

use Cake\ORM\Entity;

/**
 * @property int $id
 * @property string $email
 * @property string $password
 * @property string $name
 * @property int $account_status_master_id
 * @property int $is_email_verified
 * @property \Cake\I18n\DateTime $password_changed_at
 * @property \Cake\I18n\DateTime $password_expires_at
 * @property \Cake\I18n\DateTime $created
 * @property \Cake\I18n\DateTime $modified
 * @property \App\Model\Entity\Shared\AccountStatusMaster $account_status_master
 */
class UserAccount extends Entity
{
    /**
     * @var array<string, bool>
     */
    protected array $_accessible = [
        'email' => true,
        'password' => true,
        'name' => true,
        'account_status_master_id' => true,
        'is_email_verified' => true,
        'password_changed_at' => true,
        'password_expires_at' => true,
        'created' => true,
        'modified' => true,
        'account_status_master' => true,
    ];
}
