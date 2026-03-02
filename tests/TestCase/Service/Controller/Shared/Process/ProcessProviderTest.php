<?php
declare(strict_types=1);

namespace App\Test\TestCase\Service\Controller\Shared\Process;

use App\Security\Auth\AuthContext;
use App\Security\Auth\AuthContext\Fields;
use App\Service\Controller\Shared\Process\Process\InputProcess;
use App\Service\Controller\Shared\Process\Process\Fields\ProcessId;
use App\Service\Controller\Shared\Process\Process\Fields\ProcessParams;
use App\Service\Controller\Shared\Process\ProcessProvider;
use App\Service\Controller\Shared\ServiceInterface;
use App\Service\Controller\Shared\ServiceTrait;
use Cake\Http\ServerRequest;
use Cake\Http\Session;
use Cake\TestSuite\TestCase;
use InvalidArgumentException;

final class ProcessProviderTest extends TestCase
{
    private ProcessProvider $provider;

    protected function setUp(): void
    {
        parent::setUp();

        // AuthContext スタブ
        $authContext = new class() implements AuthContext {
            public function getType(): Fields\Type
            {
                return new Fields\Type(AuthContext::TYPE_ANONYMOUS);
            }

            public function getAccountId(): Fields\AccountId
            {
                return new Fields\AccountId(null);
            }
        };

        // Session モック
        $session = $this->createMock(Session::class);
        $session->method('check')->willReturn(true);
        $session->method('read')->willReturn(['foo' => 'bar']);

        // Request モック
        $request = $this->createMock(ServerRequest::class);
        $request->method('getSession')->willReturn($session);

        $this->provider = new ProcessProvider(
            datetime: new \DateTimeImmutable(),
            request: $request,
            authContext: $authContext,
        );
    }

    public function testProvideReturnsProcess(): void
    {
        $processId = new ProcessId('abc123');

        $process = $this->provider->provide(
            InputProcess::class,
            DummyService::class,
            $processId
        );

        $this->assertInstanceOf(InputProcess::class, $process);
        $this->assertInstanceOf(ProcessParams::class, $process->getProcessParams());
    }

    public function testProvideReturnsNullWhenSessionMissing(): void
    {
        $session = $this->createMock(Session::class);
        $session->method('check')->willReturn(false);

        $request = $this->createMock(ServerRequest::class);
        $request->method('getSession')->willReturn($session);
        // AuthContext スタブ
        $authContext = new class() implements AuthContext {
            public function getType(): Fields\Type
            {
                return new Fields\Type(AuthContext::TYPE_ANONYMOUS);
            }

            public function getAccountId(): Fields\AccountId
            {
                return new Fields\AccountId(null);
            }
        };

        $this->provider = new ProcessProvider(
            datetime: new \DateTimeImmutable(),
            request: $request,
            authContext: $authContext,
        );

        $processId = new ProcessId('123456');

        $result = $this->provider->provide(
            InputProcess::class,
            DummyService::class,
            $processId
        );

        $this->assertNull($result);
    }

    public function testThrowsOnInvalidProcessClass(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Process class must implement');

        $this->provider->provide(
            \stdClass::class,
            DummyService::class,
            new ProcessId('x')
        );
    }

    public function testThrowsOnInvalidServiceClass(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Service class must implement');

        $this->provider->provide(
            InputProcess::class,
            \stdClass::class,
            new ProcessId('x')
        );
    }
}

final class DummyService implements ServiceInterface
{
    use ServiceTrait;
}