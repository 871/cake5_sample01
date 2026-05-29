<?php
declare(strict_types=1);

namespace App\Application\Controller\User;

use App\Infrastructure\Persistence\Cake\User\RefreshTokensRepository;
use App\Security\Auth\UserTokenService;
use App\Security\Input\Cast;
use App\Application\Controller\Shared\ServiceInterface;
use App\Application\Controller\Shared\ServiceTrait;

final class Logout implements ServiceInterface
{
    use ServiceTrait;

    /**
     * @return array<int, string>
     */
    public function logout(): array
    {
        $refreshToken = Cast::toStringOrNull(
            $this->request->getCookie(UserTokenService::REFRESH_TOKEN_COOKIE),
        );
        $refreshAuth = (new UserTokenService())->readRefreshToken($refreshToken);
        $refreshTokenId = Cast::toStringOrNull($refreshAuth['refresh_token_id'] ?? null);
        if ($refreshTokenId !== null) {
            (new RefreshTokensRepository())->delete($refreshTokenId);
        }

        return (new UserTokenService())->createExpiredCookieHeaders();
    }
}
