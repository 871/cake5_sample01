<?php
declare(strict_types=1);

namespace App\Test\TestCase\Application\Controller\Shared\Process;

use App\Security\Auth\AuthContext\Fields;
use App\Security\Auth\AuthContext\Fields\AccountId\AdminAccountId;
use App\Security\Auth\AuthContext\Fields\AccountId\AnonymousAccountId;
use App\Application\Controller\Shared\Process\SessionKey;
use App\Application\Controller\Shared\Process\Process\Fields\ProcessId;
use Cake\TestSuite\TestCase;

final class SessionKeyTest extends TestCase
{
    public function testToStringBuildsCorrectKey(): void
    {
        $type = new Fields\Type('anonymous');
        $accountId = new AnonymousAccountId('0');
        $processId = new ProcessId('abc123');

        $sessionKey = new SessionKey(
            prefix: 'process',
            type: $type,
            accountId: $accountId,
            processId: $processId,
        );

        $expected = 'process.anonymous.0.000000000abc123';

        $this->assertSame($expected, $sessionKey->toString());
    }

    public function testToStringAndMagicMethodAreEqual(): void
    {
        $type = new Fields\Type('anonymous');
        $accountId = new AdminAccountId('900001');
        $processId = new ProcessId('xyz');

        $sessionKey = new SessionKey(
            prefix: 'p',
            type: $type,
            accountId: $accountId,
            processId: $processId,
        );

        $this->assertSame(
            $sessionKey->toString(),
            (string)$sessionKey
        );
    }
}
