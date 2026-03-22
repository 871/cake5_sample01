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
    private function makeRequest(string $account_id, bool $hasSession, string $url = '/v1/ad/test_account/'): ServerRequest
    {
        $session = $this->createMock(Session::class);
        $session->method('check')
            ->willReturn($hasSession);

        $request = new ServerRequest([
            'url' => $url,
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
        $request = $this->makeRequest('test_account', false, '/v1/ad/test_account/');
        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->expects($this->never())->method('handle');

        $response = $middleware->process($request, $handler);

        $this->assertSame(
            '/v1/ad/login?redirect=' . urlencode('/v1/ad/test_account/'),
            $response->getHeaderLine('Location'),
        );
    }

    public function testRedirectsToLoginWithQueryStringWhenNotAuthenticated(): void
    {
        $middleware = new AdminAuthMiddleware();
        $request = $this->makeRequest('test_account', false, '/v1/ad/test_account/?foo=bar');
        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->expects($this->never())->method('handle');

        $response = $middleware->process($request, $handler);

        $this->assertSame(
            '/v1/ad/login?redirect=' . urlencode('/v1/ad/test_account/?foo=bar'),
            $response->getHeaderLine('Location'),
        );
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
