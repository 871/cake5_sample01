<?php
declare(strict_types=1);

namespace App\Model\Entity\Log;

use Cake\ORM\Entity;

/**
 * PageAccessLog Entity
 *
 * @property string $id
 * @property \Cake\I18n\DateTime $accessed
 * @property string $account_type
 * @property int|null $account_id
 * @property string $method
 * @property string $path
 * @property string|null $query_string
 * @property string|null $post_keys
 * @property string|null $route_name
 * @property string|null $referer
 * @property string|null $ip_address
 * @property string|null $user_agent
 * @property \Cake\I18n\DateTime $created
 */
class PageAccessLog extends Entity
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
        'accessed' => true,
        'account_type' => true,
        'account_id' => true,
        'method' => true,
        'path' => true,
        'query_string' => true,
        'post_keys' => true,
        'route_name' => true,
        'referer' => true,
        'ip_address' => true,
        'user_agent' => true,
        'created' => true,
    ];
}
