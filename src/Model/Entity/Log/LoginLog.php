<?php
declare(strict_types=1);

namespace App\Model\Entity\Log;

use Cake\ORM\Entity;

/**
 * LoginLog Entity
 *
 * @property string $id
 * @property string $login_id
 * @property string $login_actor_type
 * @property int|null $account_id
 * @property int|null $impersonator_account_id
 * @property string $login_result
 * @property string $ip_address
 * @property string|null $user_agent
 * @property string|null $failure_reason_code
 * @property \Cake\I18n\DateTime $logged_in_at
 * @property \Cake\I18n\DateTime $created
 */
class LoginLog extends Entity
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
        'login_id' => true,
        'login_actor_type' => true,
        'account_id' => true,
        'impersonator_account_id' => true,
        'login_result' => true,
        'ip_address' => true,
        'user_agent' => true,
        'failure_reason_code' => true,
        'logged_in_at' => true,
        'created' => true,
    ];
}
