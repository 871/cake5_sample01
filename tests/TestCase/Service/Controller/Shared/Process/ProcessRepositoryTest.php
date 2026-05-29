<?php
declare(strict_types=1);

namespace App\Test\TestCase\Service\Controller\Shared\Process;

use App\Security\Auth\AuthContext\AnonymousAuthContext;
use App\Application\Controller\Shared\Process\Process\InputProcess;
use App\Application\Controller\Shared\Process\Process\Fields\ProcessId;
use App\Application\Controller\Shared\Process\Process\Fields\ProcessParams;
use App\Application\Controller\Shared\Process\ProcessRepository;
use Cake\Http\ServerRequest;
use Cake\Http\Session;
use Cake\TestSuite\TestCase;
use DomainException;

final class ProcessRepositoryTest extends TestCase
{
    private ProcessRepository $repository;

    /**
     * @var Session&\PHPUnit\Framework\MockObject\MockObject
     */
    private Session $session;

    protected function setUp(): void
    {
        parent::setUp();

        $authContext = new AnonymousAuthContext($this->createMock(ServerRequest::class));

        $this->session = $this->createMock(Session::class);

        $request = $this->createMock(ServerRequest::class);
        $request->method('getSession')->willReturn($this->session);

        $this->repository = new ProcessRepository(
            datetime: new \DateTimeImmutable(),
            request: $request,
            authContext: $authContext,
        );
    }

    public function testSaveWritesToSession(): void
    {
        $process = new InputProcess(
            processId: new ProcessId('testid'),
            processParams: new ProcessParams(['foo' => 'bar'])
        );

        $this->session->method('check')->willReturn(true);

        $this->session
            ->expects($this->once())
            ->method('write');

        $this->repository->save(
            $process
        );
    }

    public function testSaveThrowsWhenSessionMissing(): void
    {
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('An invalid Process Instance was set');

        $process = new InputProcess(
            processId: new ProcessId('testid'),
            processParams: new ProcessParams(['foo' => 'bar'])
        );

        $this->session->method('check')->willReturn(false);

        $this->repository->save(
            $process
        );
    }
}
