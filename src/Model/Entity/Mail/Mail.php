<?php
declare(strict_types=1);

namespace App\Model\Entity\Mail;

use Cake\ORM\Entity;

class Mail extends Entity
{
    /**
     * @var array<string, bool>
     */
    protected array $_accessible = [
        'related_data_key' => true,
        'send_status' => true,
        'send_scheduled_at' => true,
        'title' => true,
        'body' => true,
        'mail_to' => true,
        'mail_cc' => true,
        'mail_bcc' => true,
        'mail_received_check' => true,
        'mail_return_path' => true,
        'created' => true,
        'created_by' => true,
        'created_ip' => true,
        'modified' => true,
        'modified_by' => true,
        'modified_ip' => true,
    ];
}
