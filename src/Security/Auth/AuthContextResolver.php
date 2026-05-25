<?php
declare(strict_types=1);

namespace App\Security\Auth;

use Cake\Http\ServerRequest;

final class AuthContextResolver
{
    /**
     * @return \App\Security\Auth\AuthContext
     */
    public static function resolve(ServerRequest $request): AuthContext
    {
        preg_match('/^\/v1\/([^\/]+)\/[\d]+(?:\/.*)?$/', $request->getPath(), $matches);
        $type = $matches[1] ?? '';

        return match ($type) {
            // 管理者アカウントのAuthContextを生成する
            'ad' => new AuthContext\AdminAuthContext($request),
            // ユーザーアカウントのAuthContextを生成する
            'us' => new AuthContext\UserAuthContext($request),
            // 未認証の場合は匿名のAuthContextを生成する
            default => new AuthContext\AnonymousAuthContext($request),
        };
    }
}
