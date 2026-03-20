<?php
declare(strict_types=1);

namespace App\Security\Auth;

use App\Security\Auth\AuthContext\Fields;
use Cake\Http\ServerRequest;
use App\Security\Input\StrictCast;

final class AuthContextResolver
{
    /**
     * @param \Cake\Http\ServerRequest $request
     */
    private function __construct(
        private readonly ServerRequest $request,
    ) {
    }

    /**
     * @return \App\Security\Auth\AuthContext
     */
    public static function resolve(ServerRequest $request): AuthContext
    {
        $type = StrictCast::toString(
            preg_replace('/^\/([^\/]+)\/([^\/]+)\/([^\/]+)\/.*$/', '$2', $request->getPath()),
        );

        return match ($type) {
            // 管理者アカウントのAuthContextを生成する
            'ad' => (new self($request))->createAuthContextForAdminAccount(),
            // 未認証の場合は匿名のAuthContextを生成する
            default => (new self($request))->createAuthContextForAnonymous(),
        };
    }

    /**
     * 管理者アカウントのAuthContextを生成する
     *
     * @return \App\Security\Auth\AuthContext\AdminAuthContext
     */
    private function createAuthContextForAdminAccount(): AuthContext\AdminAuthContext
    {
        $auth = (new AuthSession(
            request: $this->request, 
            type: Fields\Type::TYPE_ADMIN,
            account_id: StrictCast::toString($this->request->getParam('account_id')),
        ))->read();

        return new AuthContext\AdminAuthContext(
            type: new Fields\Type(Fields\Type::TYPE_ADMIN),
            accountId: new Fields\AccountId\AdminAccountId($auth['account_id']),
            accountEmail: new Fields\AccountEmail\AdminAccountEmail($auth['account_email']),
            accountName: new Fields\AccountName\AdminAccountName($auth['account_name']),
            accountStatusMasterId: new Fields\AccountStatusMasterId\AdminAccountStatusMasterId($auth['account_status_master_id']),
            accountStatusMasterName: new Fields\AccountStatusMasterName\AdminAccountStatusMasterName($auth['account_status_master_name']),
            accountStatusMasterCode: new Fields\AccountStatusMasterCode\AdminAccountStatusMasterCode($auth['account_status_master_code']),
            isEmailVerified: new Fields\IsEmailVerified($auth['is_email_verified']),
            passwordChangedAt: new Fields\PasswordChangedAt($auth['password_changed_at']),
            passwordExpiresAt: new Fields\PasswordExpiresAt($auth['password_expires_at']),
            created: new Fields\Created($auth['created']),
            modified: new Fields\Modified($auth['modified']),
            logined: new Fields\Logined($auth['logined']),
        );
    }

    /**
     * 匿名のAuthContextを生成する
     *
     * @return \App\Security\Auth\AuthContext\AnonymousAuthContext
     */
    private function createAuthContextForAnonymous(): AuthContext\AnonymousAuthContext
    {
        return new AuthContext\AnonymousAuthContext(
            type: new Fields\Type(Fields\Type::TYPE_ANONYMOUS),
            accountName: new Fields\AccountName\AnonymousAccountName(),
        );
    }
}
