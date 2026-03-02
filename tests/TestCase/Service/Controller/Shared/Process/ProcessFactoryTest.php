<?php
declare(strict_types=1);

namespace App\Test\TestCase\Service\Controller\Shared\Process;

use App\Security\Auth\AuthContext;
use App\Security\Auth\AuthContext\Fields;
use App\Service\Controller\Shared\Process\ProcessFactory;
use App\Service\Controller\Shared\Process\Process\Fields\ProcessParams;
use App\Service\Controller\Shared\Process\Process\InputProcess;
use Cake\Http\ServerRequest;
use Cake\TestSuite\TestCase;
use InvalidArgumentException;

final class ProcessFactoryTest extends TestCase
{
    private ProcessFactory $factory;

    protected function setUp(): void
    {
        parent::setUp();

        // authContext と request のモックを注入
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

        $request = $this->createMock(ServerRequest::class);

        $this->factory = new ProcessFactory(
            datetime : new \DateTimeImmutable(),
            request : $request,
            authContext : $authContext
        );
    }

    public function testStartCreatesProcess(): void
    {
        $params = new ProcessParams(['foo' => 'bar']);
        $process = $this->factory->start(
            InputProcess::class,
            get_class($this->factory),
            $params
        );

        $this->assertInstanceOf(InputProcess::class, $process);
        $this->assertSame($params, $process->getProcessParams());
    }

    public function testStartThrowsOnInvalidProcessClass(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Process class must implement');

        $params = new ProcessParams([]);
        $this->factory->start(
            \stdClass::class, // Process ではない
            get_class($this->factory),
            $params
        );
    }

    public function testStartThrowsOnInvalidServiceClass(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Service class must implement');

        $params = new ProcessParams([]);
        $this->factory->start(
            InputProcess::class,
            \stdClass::class, // ServiceInterface ではない
            $params
        );
    }
}
