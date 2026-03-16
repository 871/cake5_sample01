<?php
declare(strict_types=1);

namespace App\Model\Entity\Admin;

use Cake\ORM\Entity;

/**
 * AdminRole Entity
 *
 * @property string $id
 * @property string $name
 */
class AdminRole extends Entity
{
    protected array $_accessible = [
        'name' => true,
    ];
}
