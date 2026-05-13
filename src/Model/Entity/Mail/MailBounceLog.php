<?php
declare(strict_types=1);

namespace App\Model\Entity\Mail;

use Cake\ORM\Entity;

/**
 * MailBounceLog Entity
 *
 * @property string $id
 * @property int|null $mail_id
 * @property string|null $original_message_id
 * @property string $bounced_email
 * @property string|null $recipient_type
 * @property string|null $action
 * @property string|null $status_code
 * @property string|null $diagnostic_code
 * @property string $bounce_type
 * @property string|null $remote_mta
 * @property string|null $reporting_mta
 * @property \Cake\I18n\DateTime|null $arrival_date
 * @property \Cake\I18n\DateTime $bounced_at
 * @property string|null $raw_headers
 * @property string|null $raw_body
 * @property string|null $raw_message
 * @property mixed $parsed_json
 * @property string|null $provider
 * @property bool $is_auto_generated
 * @property \Cake\I18n\DateTime $created
 * @property int|null $created_by
 * @property string|null $created_ip
 */
class MailBounceLog extends Entity
{
    /**
     * @var array<string, bool>
     */
    protected array $_accessible = [
        'mail_id' => true,
        'original_message_id' => true,
        'bounced_email' => true,
        'recipient_type' => true,
        'action' => true,
        'status_code' => true,
        'diagnostic_code' => true,
        'bounce_type' => true,
        'remote_mta' => true,
        'reporting_mta' => true,
        'arrival_date' => true,
        'bounced_at' => true,
        'raw_headers' => true,
        'raw_body' => true,
        'raw_message' => true,
        'parsed_json' => true,
        'provider' => true,
        'is_auto_generated' => true,
        'created' => true,
        'created_by' => true,
        'created_ip' => true,
    ];
}
