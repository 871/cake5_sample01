<?php
declare(strict_types=1);

namespace App\Middleware\User;

use App\Domain\Exception\RepositoryException;
use App\Domain\User\UserAccounts\Entity\UserAccount;
use App\Domain\User\UserAccounts\Repository\UserAccountsRepository as UserAccountsRepositoryInterface;
use App\Domain\User\UserAccounts\ValueObject as UserAccountVo;
use App\Infrastructure\Persistence\Cake\User\RefreshTokensRepository;
use App\Infrastructure\Persistence\Cake\User\UserAccountsRepository;
use App\Security\Auth\AuthContext\Fields\Type;
use App\Security\Auth\UserTokenService;
use App\Security\Input\Cast;
use App\Security\Input\StrictCast;
use Cake\Http\Response;
use DateTimeImmutable;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class UserAuthMiddleware implements MiddlewareInterface
{
    /**
     * @param \App\Security\Auth\UserTokenService $tokenService
     * @param \App\Domain\User\UserAccounts\Repository\UserAccountsRepository $userAccountsRepository
     * @param \App\Infrastructure\Persistence\Cake\User\RefreshTokensRepository $refreshTokensRepository
     */
    public function __construct(
        private readonly UserTokenService $tokenService = new UserTokenService(),
        private readonly UserAccountsRepositoryInterface $userAccountsRepository = new UserAccountsRepository(),
        private readonly RefreshTokensRepository $refreshTokensRepository = new RefreshTokensRepository(),
    ) {
    }

    /**
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Server\RequestHandlerInterface $handler
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        /** @var \Cake\Http\ServerRequest $request */
        $account_id = StrictCast::toString($request->getParam('account_id'));
        $accessToken = Cast::toStringOrNull($request->getCookie(UserTokenService::ACCESS_TOKEN_COOKIE));
        $accessAuth = $this->tokenService->readAccessToken($accessToken);

        if ($this->isValidAuth($accessAuth, $account_id)) {
            return $handler->handle($request->withAttribute(UserTokenService::REQUEST_ATTRIBUTE, $accessAuth));
        }

        $refreshToken = Cast::toStringOrNull($request->getCookie(UserTokenService::REFRESH_TOKEN_COOKIE));
        $refreshAuth = $this->tokenService->readRefreshToken($refreshToken);
        $now = new DateTimeImmutable();
        if (
            $refreshAuth !== null
            && ($refreshAuth['account_id'] ?? null) === $account_id
            && isset($refreshAuth['refresh_token_id'])
            && $this->refreshTokensRepository->isValid($refreshAuth['refresh_token_id'], $account_id, $now)
        ) {
            try {
                $account = $this->userAccountsRepository->read(UserAccountVo\Id::fromString($account_id));
            } catch (RepositoryException) {
                $account = null;
            }

            if ($account !== null && $this->canAuthenticate($account)) {
                $tokenSet = $this->tokenService->createTokenSet($account, $now);
                $this->refreshTokensRepository->rotate(
                    currentId: $refreshAuth['refresh_token_id'],
                    userAccountId: $account_id,
                    nextId: $tokenSet['refresh_token_id'],
                    expiresAt: $tokenSet['refresh_token_expires_at'],
                    now: $now,
                );
                $response = $handler->handle(
                    $request->withAttribute(UserTokenService::REQUEST_ATTRIBUTE, $tokenSet['auth']),
                );

                return $this->tokenService->withCookieHeaders($response, $tokenSet['set_cookie_headers']);
            }
        }

        $uri = $request->getUri();
        $currentPath = $uri->getPath();
        $currentQuery = $uri->getQuery();
        $currentUrl = $currentQuery !== '' ? $currentPath . '?' . $currentQuery : $currentPath;
        $request->getSession()->write('Flash.flash', [[
            'message' => 'ログアウトしました。',
            'key' => 'flash',
            'element' => 'Flash/error',
            'params' => [],
        ]]);
        $response = (new Response())->withLocation('/v1/us/login?redirect=' . urlencode($currentUrl));

        return $this->tokenService->withCookieHeaders($response, $this->tokenService->createExpiredCookieHeaders());
    }

    /**
     * @param ?array<string, string> $auth
     * @param string $account_id
     * @return bool
     */
    private function isValidAuth(?array $auth, string $account_id): bool
    {
        return $auth !== null
            && ($auth['type'] ?? null) === Type::TYPE_USER
            && ($auth['account_id'] ?? null) === $account_id;
    }

    /**
     * @param \App\Domain\User\UserAccounts\Entity\UserAccount $account
     * @return bool
     */
    private function canAuthenticate(UserAccount $account): bool
    {
        if ($account->passwordExpiresAt()->toDateTime()->getTimestamp() < (new DateTimeImmutable())->getTimestamp()) {
            return false;
        }

        return in_array($account->accountStatusMasterCode()->toString(), [
            UserAccountVo\AccountStatusMasterCode::ACTIVE,
            UserAccountVo\AccountStatusMasterCode::PENDING,
        ], true);
    }
}
