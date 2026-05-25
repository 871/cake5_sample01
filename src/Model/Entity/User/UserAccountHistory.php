<?php
declare(strict_types=1);

namespace App\Model\Entity\User;

use Cake\ORM\Entity;

/**
 * UserAccountHistory Entity
 *
 * @property string $id
 * @property int $user_account_id
 * @property string $email
 * @property string $password
 * @property string $name
 * @property int $account_status_master_id
 * @property int $is_email_verified
 * @property \Cake\I18n\DateTime $password_changed_at
 * @property \Cake\I18n\DateTime $password_expires_at
 * @property \Cake\I18n\DateTime $created
 * @property int|null $created_by
 * @property string|null $created_ip
 * @property \Cake\I18n\DateTime $modified
 * @property int|null $modified_by
 * @property string|null $modified_ip
 * @property string $operation_type
 * @property \Cake\I18n\DateTime $history_created
 *
 * @property \App\Model\Entity\User\UserAccount $user_account
 * @property \App\Model\Entity\Shared\AccountStatusMaster $account_status_master
 */
class UserAccountHistory extends Entity
{
    /**
     * @var array<string, bool>
     */
    protected array $_accessible = [
        'id' => true,
        'user_account_id' => true,
        'email' => true,
        'password' => true,
        'name' => true,
        'account_status_master_id' => true,
        'is_email_verified' => true,
        'password_changed_at' => true,
        'password_expires_at' => true,
        'created' => true,
        'created_by' => true,
        'created_ip' => true,
        'modified' => true,
        'modified_by' => true,
        'modified_ip' => true,
        'operation_type' => true,
        'history_created' => true,
        'user_account' => true,
        'account_status_master' => true,
    ];
}
