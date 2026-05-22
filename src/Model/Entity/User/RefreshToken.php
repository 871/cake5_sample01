<?php
declare(strict_types=1);

namespace App\Model\Entity\User;

use Cake\ORM\Entity;

/**
 * RefreshToken Entity
 *
 * @property string $id
 * @property int $user_account_id
 * @property \Cake\I18n\DateTime $expires_at
 * @property \Cake\I18n\DateTime $created
 * @property int|null $created_by
 * @property string|null $created_ip
 * @property \Cake\I18n\DateTime $modified
 * @property int|null $modified_by
 * @property string|null $modified_ip
 *
 * @property \App\Model\Entity\User\UserAccount $user_account
 */
class RefreshToken extends Entity
{
    /**
     * @var array<string, bool>
     */
    protected array $_accessible = [
        'id' => true,
        'user_account_id' => true,
        'expires_at' => true,
        'created' => true,
        'created_by' => true,
        'created_ip' => true,
        'modified' => true,
        'modified_by' => true,
        'modified_ip' => true,
        'user_account' => true,
    ];
}
