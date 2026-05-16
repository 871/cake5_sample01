<?php
declare(strict_types=1);

namespace App\Model\Entity\Grant;

use Cake\ORM\Entity;

/**
 * GrantPermission Entity
 *
 * @property int $id
 * @property string $account_type
 * @property string $code
 * @property string $name
 * @property string|null $description
 * @property int $sort
 * @property int $is_active
 * @property \Cake\I18n\DateTime $created
 * @property \Cake\I18n\DateTime $modified
 *
 * @property \App\Model\Entity\Grant\GrantRolePermission[] $grant_role_permissions
 * @property \App\Model\Entity\Grant\GrantAccountPermission[] $grant_account_permissions
 */
class GrantPermission extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * @var array<string, bool>
     */
    protected array $_accessible = [
        'account_type' => true,
        'code' => true,
        'name' => true,
        'description' => true,
        'sort' => true,
        'is_active' => true,
        'created' => true,
        'modified' => true,
        'grant_role_permissions' => true,
        'grant_account_permissions' => true,
    ];

    /**
     * Fields that are excluded from JSON versions of the entity.
     *
     * @var array<string>
     */
    protected array $_hidden = [];
}
