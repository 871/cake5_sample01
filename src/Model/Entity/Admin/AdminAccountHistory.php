<?php
declare(strict_types=1);

namespace App\Model\Entity\Admin;

use Cake\ORM\Entity;

/**
 * AdminAccountHistory Entity
 *
 * @property string $id
 * @property int $admin_account_id
 * @property string $email
 * @property string $password
 * @property string $name
 * @property string|null $admin_note
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
 * @property \App\Model\Entity\Admin\AdminAccount $admin_account
 * @property \App\Model\Entity\Shared\AccountStatusMaster $account_status_master
 */
class AdminAccountHistory extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array<string, bool>
     */
    protected array $_accessible = [
        'admin_account_id' => true,
        'email' => true,
        'password' => true,
        'name' => true,
        'admin_note' => true,
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
        'admin_account' => true,
        'account_status_master' => true,
    ];

    /**
     * Fields that are excluded from JSON versions of the entity.
     *
     * @var array<string>
     */
    protected array $_hidden = [
        'password',
    ];
}
