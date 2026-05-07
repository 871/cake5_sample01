<?php
declare(strict_types=1);

namespace App\Model\Table\Mail;

use App\Model\Entity\Mail\MailReceivedCheckLog;
use Cake\ORM\Table;

final class MailReceivedCheckLogsTable extends Table
{
    /**
     * @param array<string, mixed> $config
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setEntityClass(MailReceivedCheckLog::class);
        $this->setTable('mail_received_check_logs');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('Mails', [
            'className' => MailsTable::class,
            'foreignKey' => 'mail_id',
            'joinType' => 'INNER',
        ]);
    }
}
