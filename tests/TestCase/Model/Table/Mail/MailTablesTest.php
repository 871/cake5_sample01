<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table\Mail;

use App\Model\Table\Mail\MailBounceLogsTable;
use App\Model\Table\Mail\MailReceivedCheckLogsTable;
use App\Model\Table\Mail\MailSentLogsTable;
use App\Model\Table\Mail\MailsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

final class MailTablesTest extends TestCase
{
    public function testFetchMailsTable(): void
    {
        $table = TableRegistry::getTableLocator()->get('Mails', [
            'className' => MailsTable::class,
        ]);

        $this->assertInstanceOf(MailsTable::class, $table);
    }

    public function testFetchMailSentLogsTable(): void
    {
        $table = TableRegistry::getTableLocator()->get('MailSentLogs', [
            'className' => MailSentLogsTable::class,
        ]);

        $this->assertInstanceOf(MailSentLogsTable::class, $table);
    }

    public function testFetchMailReceivedCheckLogsTable(): void
    {
        $table = TableRegistry::getTableLocator()->get('MailReceivedCheckLogs', [
            'className' => MailReceivedCheckLogsTable::class,
        ]);

        $this->assertInstanceOf(MailReceivedCheckLogsTable::class, $table);
    }

    public function testFetchMailBounceLogsTable(): void
    {
        $table = TableRegistry::getTableLocator()->get('MailBounceLogs', [
            'className' => MailBounceLogsTable::class,
        ]);

        $this->assertInstanceOf(MailBounceLogsTable::class, $table);
    }
}
