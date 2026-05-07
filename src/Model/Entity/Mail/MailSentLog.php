<?php
declare(strict_types=1);

namespace App\Model\Entity\Mail;

use Cake\ORM\Entity;

/**
 * MailSentLog Entity
 *
 * @property string $id
 * @property int $mail_id
 * @property string $send_status
 * @property string|null $error_message
 * @property \Cake\I18n\DateTime $sent_at
 * @property \Cake\I18n\DateTime $created
 * @property int|null $created_by
 * @property string|null $created_ip
 */
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
