<?php
declare(strict_types=1);

namespace App\Security\Auth;

use App\Security\Input\StrictCast;
use Cake\Http\ServerRequest;

final class AuthContextResolver
{
    /**
     * @return \App\Security\Auth\AuthContext
     */
    public static function resolve(ServerRequest $request): AuthContext
    {
        $type = StrictCast::toString(
            preg_replace('/^\/([^\/]+)\/([^\/]+)\/([\d]+)\/.*$/', '$2', $request->getPath()),
        );

        return match ($type) {
            // 管理者アカウントのAuthContextを生成する
            'ad' => new AuthContext\AdminAuthContext($request),
            // 未認証の場合は匿名のAuthContextを生成する
            default => new AuthContext\AnonymousAuthContext($request),
        };
    }
}
