<?php
declare(strict_types=1);

namespace App\Test\TestCase\Infrastructure\Persistence\Cake\Mail;

use App\Domain\Mail\Repository\MailsRepository as DomainMailsRepository;
use App\Infrastructure\Persistence\Cake\Mail\MailsRepository;
use Cake\TestSuite\TestCase;
use ReflectionClass;

final class MailsRepositoryApiTest extends TestCase
{
    public function testDomainRepositoryMethodsAreUpdated(): void
    {
        $reflection = new ReflectionClass(DomainMailsRepository::class);

        $this->assertTrue($reflection->hasMethod('sendWaitingMails'));
        $this->assertTrue($reflection->hasMethod('checkReceivedMails'));
        $this->assertTrue($reflection->hasMethod('checkBouncedMails'));

        $this->assertFalse($reflection->hasMethod('updateSent'));
        $this->assertFalse($reflection->hasMethod('updateFailed'));
        $this->assertFalse($reflection->hasMethod('updateReceived'));
        $this->assertFalse($reflection->hasMethod('updateBounced'));
    }

    public function testInfrastructureRepositoryImplementsUpdatedMethods(): void
    {
        $reflection = new ReflectionClass(MailsRepository::class);

        $this->assertTrue($reflection->hasMethod('sendWaitingMails'));
        $this->assertTrue($reflection->hasMethod('checkReceivedMails'));
        $this->assertTrue($reflection->hasMethod('checkBouncedMails'));
    }
}
