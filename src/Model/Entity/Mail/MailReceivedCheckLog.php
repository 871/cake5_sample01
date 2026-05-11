<?php
declare(strict_types=1);

namespace App\Model\Entity\Mail;

use Cake\ORM\Entity;

/**
 * MailReceivedCheckLog Entity
 *
 * @property string $id
 * @property int $mail_id
 * @property string|null $original_message_id
 * @property string $checked_address
 * @property \Cake\I18n\DateTime $checked_at
 * @property \Cake\I18n\DateTime $created
 * @property int|null $created_by
 * @property string|null $created_ip
 */
class MailReceivedCheckLog extends Entity
{
    /**
     * @var array<string, bool>
     */
    protected array $_accessible = [
        'mail_id' => true,
        'original_message_id' => true,
        'checked_address' => true,
        'checked_at' => true,
        'created' => true,
        'created_by' => true,
        'created_ip' => true,
    ];
}
