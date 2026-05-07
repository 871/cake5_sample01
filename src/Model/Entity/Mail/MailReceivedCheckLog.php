<?php
declare(strict_types=1);

namespace App\Model\Entity\Mail;

use Cake\ORM\Entity;

class MailReceivedCheckLog extends Entity
{
    /**
     * @var array<string, bool>
     */
    protected array $_accessible = [
        'mail_id' => true,
        'checked_at' => true,
        'created' => true,
        'created_by' => true,
        'created_ip' => true,
    ];
}
