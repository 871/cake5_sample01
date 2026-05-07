<?php
declare(strict_types=1);

namespace App\Model\Entity\Mail;

use Cake\ORM\Entity;

class MailSentLog extends Entity
{
    /**
     * @var array<string, bool>
     */
    protected array $_accessible = [
        'mail_id' => true,
        'send_status' => true,
        'error_message' => true,
        'sent_at' => true,
        'created' => true,
        'created_by' => true,
        'created_ip' => true,
    ];
}
