<?php
declare(strict_types=1);

namespace App\Test\TestCase\Middleware\Admin;

use App\Middleware\Admin\AdminAuthMiddleware;
use Cake\Http\Response;
use Cake\Http\ServerRequest;
use Cake\Http\Session;
use Cake\TestSuite\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class AdminAuthMiddlewareTest extends TestCase
{
    private function makeRequest(string $account_id, bool $hasSession): ServerRequest
    {
        $session = $this->createMock(Session::class);
        $session->method('check')
            ->willReturn($hasSession);

        $request = new ServerRequest([
            'params' => ['account_id' => $account_id],
            'session' => $session,
        ]);

        return $request;
    }

    private function makeHandler(): RequestHandlerInterface
    {
        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->method('handle')
            ->willReturn(new Response());

        return $handler;
    }

    public function testRedirectsToLoginWhenNotAuthenticated(): void
    {
        $middleware = new AdminAuthMiddleware();
        $request = $this->makeRequest('test_account', false);
        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->expects($this->never())->method('handle');

        $response = $middleware->process($request, $handler);

        $this->assertSame('/v1/ad/login', $response->getHeaderLine('Location'));
    }

    public function testPassesThroughWhenAuthenticated(): void
    {
        $middleware = new AdminAuthMiddleware();
        $request = $this->makeRequest('test_account', true);
        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->expects($this->once())->method('handle')->willReturn(new Response());

        $response = $middleware->process($request, $handler);

        $this->assertInstanceOf(ResponseInterface::class, $response);
    }
}
