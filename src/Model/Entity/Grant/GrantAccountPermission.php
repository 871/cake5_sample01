<?php
declare(strict_types=1);

namespace App\Model\Entity\Grant;

use Cake\ORM\Entity;

/**
 * GrantAccountPermission Entity
 *
 * @property string $id
 * @property string $account_type
 * @property int $account_id
 * @property int $grant_permission_id
 * @property \DateTimeInterface $created
 * @property \DateTimeInterface $modified
 *
 * @property \App\Model\Entity\Grant\GrantPermission $grant_permission
 */
class GrantAccountPermission extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * @var array<string, bool>
     */
    protected array $_accessible = [
        'account_type' => true,
        'account_id' => true,
        'grant_permission_id' => true,
        'created' => true,
        'modified' => true,
        'grant_permission' => true,
    ];

    /**
     * Fields that are excluded from JSON versions of the entity.
     *
     * @var array<string>
     */
    protected array $_hidden = [];
}
