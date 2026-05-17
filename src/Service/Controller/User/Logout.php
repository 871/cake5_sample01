<?php
declare(strict_types=1);

namespace App\Service\Controller\User;

use App\Security\Auth\UserTokenService;
use App\Service\Controller\Shared\ServiceInterface;
use App\Service\Controller\Shared\ServiceTrait;

final class Logout implements ServiceInterface
{
    use ServiceTrait;

    /**
     * @return array<int, string>
     */
    public function logout(): array
    {
        return (new UserTokenService())->createExpiredCookieHeaders();
    }
}
