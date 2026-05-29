<?php
declare(strict_types=1);

namespace App\Test\TestCase\Service\Controller\Shared\Process;

use App\Security\Auth\AuthContext\AnonymousAuthContext;
use App\Service\Controller\Shared\Process\ProcessFactory;
use App\Service\Controller\Shared\Process\Process\Fields\ProcessParams;
use App\Service\Controller\Shared\Process\Process\InputProcess;
use Cake\Http\ServerRequest;
use Cake\Http\Session;
use Cake\TestSuite\TestCase;
use DomainException;

final class ProcessFactoryTest extends TestCase
{
    private ProcessFactory $factory;

    protected function setUp(): void
    {
        parent::setUp();

        $authContext = new AnonymousAuthContext($this->createMock(ServerRequest::class));

        $session = $this->createMock(Session::class);
        $session->method('check')->willReturn(false);

        $request = $this->createMock(ServerRequest::class);
        $request->method('getSession')->willReturn($session);

        $this->factory = new ProcessFactory(
            datetime: new \DateTimeImmutable(),
            request: $request,
            authContext: $authContext,
        );
    }

    public function testStartCreatesProcess(): void
    {
        $params = new ProcessParams(['foo' => 'bar']);
        $process = $this->factory->start(
            InputProcess::class,
            $params
        );

        $this->assertInstanceOf(InputProcess::class, $process);
        $this->assertSame($params, $process->getProcessParams());
    }

    public function testStartThrowsOnInvalidProcessClass(): void
    {
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Process class must implement');

        $params = new ProcessParams([]);
        $this->factory->start(
            \stdClass::class, // Process ではない
            $params
        );
    }

}
