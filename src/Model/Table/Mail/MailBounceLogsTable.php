<?php
declare(strict_types=1);

namespace App\Model\Table\Mail;

use App\Model\Entity\Mail\MailBounceLog;
use Cake\ORM\Table;

final class MailBounceLogsTable extends Table
{
    /**
     * @param array<string, mixed> $config
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setEntityClass(MailBounceLog::class);
        $this->setTable('mail_bounce_logs');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('Mails', [
            'className' => MailsTable::class,
            'foreignKey' => 'mail_id',
            'joinType' => 'INNER',
        ]);
    }
}
