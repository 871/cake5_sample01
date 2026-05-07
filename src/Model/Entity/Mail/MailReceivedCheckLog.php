<?php
declare(strict_types=1);

namespace App\Model\Entity\Mail;

use Cake\ORM\Entity;

/**
 * MailReceivedCheckLog Entity
 *
 * @property string $id
 * @property int $mail_id
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
        'checked_at' => true,
        'created' => true,
        'created_by' => true,
        'created_ip' => true,
    ];
}
