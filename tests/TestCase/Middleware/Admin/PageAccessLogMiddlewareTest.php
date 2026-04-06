<?php
declare(strict_types=1);

namespace App\Test\TestCase\Middleware\Admin;

use App\Domain\Log\PageAccessLogs\Entity\PageAccessLog;
use App\Infrastructure\Persistence\Cake\Log\PageAccessLogs\PageAccessLogsRepository;
use App\Middleware\Admin\PageAccessLogMiddleware;
use Cake\Http\Response;
use Cake\Http\ServerRequest;
use Cake\TestSuite\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class PageAccessLogMiddlewareTest extends TestCase
{
    private function makeRequest(
        string $account_id,
        string $url = '/v1/ad/test_account/',
        string $method = 'GET',
        array $params = [],
    ): ServerRequest {
        return new ServerRequest([
            'url' => $url,
            'environment' => ['REQUEST_METHOD' => $method],
            'params' => array_merge(['account_id' => $account_id], $params),
        ]);
    }

    public function testPassesThroughAndLogsAccess(): void
    {
        $repository = $this->createMock(PageAccessLogsRepository::class);
        $repository->expects($this->once())
            ->method('create')
            ->with($this->isInstanceOf(PageAccessLog::class))
            ->willReturnArgument(0);

        $middleware = new PageAccessLogMiddleware($repository);
        $request = $this->makeRequest('123');
        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->expects($this->once())->method('handle')->willReturn(new Response());

        $response = $middleware->process($request, $handler);

        $this->assertInstanceOf(ResponseInterface::class, $response);
    }

    public function testLogsCorrectAccountType(): void
    {
        $capturedEntity = null;
        $repository = $this->createMock(PageAccessLogsRepository::class);
        $repository->expects($this->once())
            ->method('create')
            ->willReturnCallback(function (PageAccessLog $entity) use (&$capturedEntity): PageAccessLog {
                $capturedEntity = $entity;

                return $entity;
            });

        $middleware = new PageAccessLogMiddleware($repository);
        $request = $this->makeRequest('123');
        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->expects($this->once())->method('handle')->willReturn(new Response());

        $middleware->process($request, $handler);

        $this->assertNotNull($capturedEntity);
        $this->assertSame('ADMIN', $capturedEntity->accountType()->value());
        $this->assertSame('123', (string)$capturedEntity->accountId()->value());
    }

    public function testContinuesWhenRepositoryThrows(): void
    {
        $repository = $this->createMock(PageAccessLogsRepository::class);
        $repository->expects($this->once())
            ->method('create')
            ->willThrowException(new \RuntimeException('DB error'));

        $middleware = new PageAccessLogMiddleware($repository);
        $request = $this->makeRequest('123');
        $handler = $this->createMock(RequestHandlerInterface::class);
        $expectedResponse = new Response();
        $handler->expects($this->once())->method('handle')->willReturn($expectedResponse);

        $response = $middleware->process($request, $handler);

        $this->assertSame($expectedResponse, $response);
    }

    public function testLogsRouteNameFromParams(): void
    {
        $capturedEntity = null;
        $repository = $this->createMock(PageAccessLogsRepository::class);
        $repository->expects($this->once())
            ->method('create')
            ->willReturnCallback(function (PageAccessLog $entity) use (&$capturedEntity): PageAccessLog {
                $capturedEntity = $entity;

                return $entity;
            });

        $middleware = new PageAccessLogMiddleware($repository);
        $request = $this->makeRequest('123', '/v1/ad/123/', 'GET', [
            'prefix' => 'Admin',
            'controller' => 'Top',
            'action' => 'index',
        ]);
        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->expects($this->once())->method('handle')->willReturn(new Response());

        $middleware->process($request, $handler);

        $this->assertNotNull($capturedEntity);
        $this->assertSame('Admin/Top::index', $capturedEntity->routeName()->value());
    }
}
