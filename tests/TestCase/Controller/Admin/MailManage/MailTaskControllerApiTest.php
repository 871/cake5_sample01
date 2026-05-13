<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller\Admin\MailManage;

use App\Controller\Admin\MailManage\MailTaskController;
use Cake\TestSuite\TestCase;
use ReflectionClass;

final class MailTaskControllerApiTest extends TestCase
{
    public function testControllerHasMailTaskActions(): void
    {
        $reflection = new ReflectionClass(MailTaskController::class);

        $this->assertTrue($reflection->hasMethod('sendWaitingMails'));
        $this->assertTrue($reflection->hasMethod('checkReceivedMails'));
        $this->assertTrue($reflection->hasMethod('checkBouncedMails'));
    }
}
