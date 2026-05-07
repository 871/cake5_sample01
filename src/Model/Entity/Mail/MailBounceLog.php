<?php
declare(strict_types=1);

namespace App\Model\Entity\Mail;

use Cake\ORM\Entity;

class MailBounceLog extends Entity
{
    /**
     * @var array<string, bool>
     */
    protected array $_accessible = [
        'mail_id' => true,
        'bounced_address' => true,
        'bounce_reason' => true,
        'bounced_at' => true,
        'created' => true,
        'created_by' => true,
        'created_ip' => true,
    ];
}
