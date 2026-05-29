<?php
declare(strict_types=1);

namespace App\Test\TestCase\Middleware\User;

use App\Infrastructure\Persistence\Cake\User\RefreshTokensRepository;
use App\Infrastructure\Persistence\Cake\User\UserAccountsRepository;
use App\Middleware\User\UserAuthMiddleware;
use App\Model\Entity\User\UserAccount;
use App\Security\Auth\UserTokenService;
use Cake\Http\Response;
use Cake\Http\ServerRequest;
use Cake\Http\Session;
use Cake\ORM\Entity;
use Cake\TestSuite\TestCase;
use DateTimeImmutable;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class UserAuthMiddlewareTest extends TestCase
{
    public function testRedirectsToLoginWhenNotAuthenticated(): void
    {
        $middleware = new UserAuthMiddleware(
            new UserTokenService(),
            new UserAccountsRepository(),
            new RefreshTokensRepository(),
        );
        $request = $this->makeRequest('100001', [], '/v1/us/100001/');
        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->expects($this->never())->method('handle');

        $response = $middleware->process($request, $handler);

        $this->assertSame('/v1/us/login?redirect=' . urlencode('/v1/us/100001/'), $response->getHeaderLine('Location'));
        $this->assertCount(2, $response->getHeader('Set-Cookie'));
    }

    public function testPassesThroughWhenAccessTokenIsValid(): void
    {
        $tokenService = new UserTokenService();
        $tokenSet = $tokenService->createTokenSet($this->makeAccount(), new DateTimeImmutable());
        $repository = $this->createMock(UserAccountsRepository::class);
        $refreshTokensRepository = $this->createMock(RefreshTokensRepository::class);
        $middleware = new UserAuthMiddleware($tokenService, $repository, $refreshTokensRepository);
        $request = $this->makeRequest('100001', [
            UserTokenService::ACCESS_TOKEN_COOKIE => $tokenSet['access_token'],
        ]);
        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->expects($this->once())
            ->method('handle')
            ->with($this->callback(function (ServerRequestInterface $request): bool {
                $auth = $request->getAttribute(UserTokenService::REQUEST_ATTRIBUTE);

                return is_array($auth) && ($auth['account_id'] ?? null) === '100001';
            }))
            ->willReturn(new Response());

        $response = $middleware->process($request, $handler);

        $this->assertSame('', $response->getHeaderLine('Location'));
    }

    public function testRefreshesTokensWhenRefreshTokenIsValid(): void
    {
        $tokenService = new UserTokenService();
        $tokenSet = $tokenService->createTokenSet($this->makeAccount(), new DateTimeImmutable('-20 minutes'));
        $repository = $this->createMock(UserAccountsRepository::class);
        $refreshTokensRepository = $this->createMock(RefreshTokensRepository::class);
        $repository->expects($this->once())
            ->method('read')
            ->with('100001')
            ->willReturn($this->makeAccount());
        $refreshTokensRepository->expects($this->once())
            ->method('isValid')
            ->with($tokenSet['refresh_token_id'], '100001', $this->isInstanceOf(DateTimeImmutable::class))
            ->willReturn(true);
        $refreshTokensRepository->expects($this->once())
            ->method('rotate')
            ->with(
                $tokenSet['refresh_token_id'],
                '100001',
                $this->isType('string'),
                $this->isInstanceOf(DateTimeImmutable::class),
                $this->isInstanceOf(DateTimeImmutable::class),
            );
        $middleware = new UserAuthMiddleware($tokenService, $repository, $refreshTokensRepository);
        $request = $this->makeRequest('100001', [
            UserTokenService::ACCESS_TOKEN_COOKIE => $tokenSet['access_token'],
            UserTokenService::REFRESH_TOKEN_COOKIE => $tokenSet['refresh_token'],
        ]);
        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->expects($this->once())
            ->method('handle')
            ->with($this->callback(function (ServerRequestInterface $request): bool {
                $auth = $request->getAttribute(UserTokenService::REQUEST_ATTRIBUTE);

                return is_array($auth) && ($auth['account_id'] ?? null) === '100001';
            }))
            ->willReturn(new Response());

        $response = $middleware->process($request, $handler);

        $this->assertCount(2, $response->getHeader('Set-Cookie'));
    }

    /**
     * @param array<string, string> $cookies
     */
    private function makeRequest(string $account_id, array $cookies = [], string $url = '/v1/us/100001/'): ServerRequest
    {
        $session = $this->createMock(Session::class);

        return (new ServerRequest([
            'url' => $url,
            'params' => ['account_id' => $account_id],
            'session' => $session,
        ]))->withCookieParams($cookies);
    }

    private function makeAccount(): UserAccount
    {
        $account = new UserAccount([
            'id' => 100001,
            'email' => 'user@example.com',
            'password' => 'hashed-password',
            'name' => 'Sample User',
            'account_status_master_id' => 200,
            'is_email_verified' => 1,
            'password_changed_at' => new DateTimeImmutable('2026-01-01 00:00:00'),
            'password_expires_at' => new DateTimeImmutable('+1 year'),
            'created' => new DateTimeImmutable('2026-01-01 00:00:00'),
            'modified' => new DateTimeImmutable('2026-01-01 00:00:00'),
        ]);
        $account->set('account_status_master', new Entity([
            'code' => 'ACTIVE',
            'name' => '有効',
        ]));

        return $account;
    }
}
