<?php
declare(strict_types=1);

namespace App\Test\TestCase\Service\Controller\Shared\Process;

use App\Security\Auth\AuthContext;
use App\Security\Auth\AuthContext\Fields;
use App\Service\Controller\Shared\Process\Process\InputProcess;
use App\Service\Controller\Shared\Process\Process\Fields\ProcessId;
use App\Service\Controller\Shared\Process\Process\Fields\ProcessParams;
use App\Service\Controller\Shared\Process\ProcessRepository;
use App\Service\Controller\Shared\ServiceInterface;
use App\Service\Controller\Shared\ServiceTrait;
use Cake\Http\ServerRequest;
use Cake\Http\Session;
use Cake\TestSuite\TestCase;
use InvalidArgumentException;

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

        $authContext = new class() implements AuthContext {
            public function getType(): Fields\Type
            {
                return new Fields\Type(AuthContext::TYPE_ANONYMOUS);
            }

            public function getAccountId(): Fields\AccountId
            {
                return new Fields\AccountId(10001);
            }
        };

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
            ProcessRepositoryTestDummyService::class,
            $process
        );
    }

    public function testSaveThrowsWhenSessionMissing(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('An invalid Process Instance was set');

        $process = new InputProcess(
            processId: new ProcessId('testid'),
            processParams: new ProcessParams(['foo' => 'bar'])
        );

        $this->session->method('check')->willReturn(false);

        $this->repository->save(
            ProcessRepositoryTestDummyService::class,
            $process
        );
    }

    public function testSaveThrowsOnInvalidServiceClass(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Service class must implement');

        $process = new InputProcess(
            processId: new ProcessId('testid'),
            processParams: new ProcessParams([])
        );

        $this->repository->save(
            \stdClass::class,
            $process
        );
    }
}

final class ProcessRepositoryTestDummyService implements ServiceInterface
{
    use ServiceTrait;
}
