<?php
declare(strict_types=1);

namespace App\Model\Entity\User;

use Cake\ORM\Entity;

/**
 * @property string $id
 * @property int $user_account_id
 * @property \Cake\I18n\DateTime $expires_at
 * @property \Cake\I18n\DateTime $created
 * @property \Cake\I18n\DateTime $modified
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
        'modified' => true,
        'user_account' => true,
    ];
}
