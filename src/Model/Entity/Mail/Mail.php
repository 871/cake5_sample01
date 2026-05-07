<?php
declare(strict_types=1);

namespace App\Model\Entity\Mail;

use Cake\ORM\Entity;

/**
 * Mail Entity
 *
 * @property int $id
 * @property string $related_data_key
 * @property string $send_status
 * @property \Cake\I18n\DateTime $send_scheduled_at
 * @property string $title
 * @property string $body
 * @property string $mail_to
 * @property string|null $mail_cc
 * @property string|null $mail_bcc
 * @property string $mail_received_check
 * @property string $mail_return_path
 * @property \Cake\I18n\DateTime $created
 * @property int|null $created_by
 * @property string|null $created_ip
 * @property \Cake\I18n\DateTime $modified
 * @property int|null $modified_by
 * @property string|null $modified_ip
 * @property array<\App\Model\Entity\Mail\MailSentLog>|null $mail_sent_logs
 * @property array<\App\Model\Entity\Mail\MailReceivedCheckLog>|null $mail_received_check_logs
 * @property array<\App\Model\Entity\Mail\MailBounceLog>|null $mail_bounce_logs
 */
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
        'mail_sent_logs' => true,
        'mail_received_check_logs' => true,
        'mail_bounce_logs' => true,
    ];
}
