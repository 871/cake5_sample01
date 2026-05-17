<?php
declare(strict_types=1);

namespace App\Middleware\User;

use App\Domain\Admin\AdminAccounts\ValueObject\AccountStatusMasterCode;
use App\Infrastructure\Persistence\Cake\User\UserAccountsRepository;
use App\Model\Entity\User\UserAccount;
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
    public function __construct(
        private readonly UserTokenService $tokenService = new UserTokenService(),
        private readonly UserAccountsRepository $userAccountsRepository = new UserAccountsRepository(),
    ) {
    }

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
        if ($refreshAuth !== null && ($refreshAuth['account_id'] ?? null) === $account_id) {
            $account = $this->userAccountsRepository->read($account_id);
            if ($account !== null && $this->canAuthenticate($account)) {
                $tokenSet = $this->tokenService->createTokenSet($account, new DateTimeImmutable());
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

    private function canAuthenticate(UserAccount $account): bool
    {
        if ($account->password_expires_at->getTimestamp() < (new DateTimeImmutable())->getTimestamp()) {
            return false;
        }

        return in_array((string)$account->account_status_master->code, [
            AccountStatusMasterCode::ACTIVE,
            AccountStatusMasterCode::PENDING,
        ], true);
    }
}
