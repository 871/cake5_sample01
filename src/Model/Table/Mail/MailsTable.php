<?php
declare(strict_types=1);

namespace App\Model\Table\Mail;

use App\Model\Entity\Mail\Mail;
use Cake\ORM\Table;

final class MailsTable extends Table
{
    /**
     * @param array<string, mixed> $config
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setEntityClass(Mail::class);
        $this->setTable('mails');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->hasMany('MailSentLogs', [
            'className' => MailSentLogsTable::class,
            'foreignKey' => 'mail_id',
            'sort' => ['MailSentLogs.created' => 'DESC'],
        ]);
        $this->hasMany('MailReceivedCheckLogs', [
            'className' => MailReceivedCheckLogsTable::class,
            'foreignKey' => 'mail_id',
            'sort' => ['MailReceivedCheckLogs.created' => 'DESC'],
        ]);
        $this->hasMany('MailBounceLogs', [
            'className' => MailBounceLogsTable::class,
            'foreignKey' => 'mail_id',
            'sort' => ['MailBounceLogs.created' => 'DESC'],
        ]);
    }
}
