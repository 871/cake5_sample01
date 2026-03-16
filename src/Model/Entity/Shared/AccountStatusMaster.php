<?php
declare(strict_types=1);

namespace App\Model\Entity\Shared;

use Cake\ORM\Entity;

/**
 * AccountStatusMaster Entity
 *
 * @property int $id
 * @property string $code
 * @property string $name
 * @property string|null $description
 * @property int $sort
 * @property int $is_active
 * @property \Cake\I18n\DateTime $created
 * @property \Cake\I18n\DateTime $modified
 *
 * @property \App\Model\Entity\Admin\AdminAccountHistory[] $admin_account_histories
 * @property \App\Model\Entity\Admin\AdminAccount[] $admin_accounts
 */
class AccountStatusMaster extends Entity
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
        'code' => true,
        'name' => true,
        'description' => true,
        'sort' => true,
        'is_active' => true,
        'created' => true,
        'modified' => true,
        'admin_account_histories' => true,
        'admin_accounts' => true,
    ];
}
