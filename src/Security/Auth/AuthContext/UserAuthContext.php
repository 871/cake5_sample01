<?php
declare(strict_types=1);

namespace App\Security\Auth\AuthContext;

use App\Security\Auth\AuthContext;
use App\Security\Auth\UserTokenService;
use App\Security\Input\Cast;
use App\Security\Input\StrictCast;
use Cake\Http\ServerRequest;

final class UserAuthContext implements AuthContext
{
    /**
     * @var array<string, string>
     */
    private array $auth;

    /**
     * @param \Cake\Http\ServerRequest $request
     */
    public function __construct(
        private readonly ServerRequest $request,
    ) {
        $auth = $this->request->getAttribute(UserTokenService::REQUEST_ATTRIBUTE);
        if (is_array($auth)) {
            /** @var array<string, mixed> $auth */
            $this->auth = array_map(
                static fn (mixed $value): string => StrictCast::toString($value),
                $auth,
            );

            return;
        }

        $this->auth = (new UserTokenService())->readAccessToken(
            Cast::toStringOrNull($this->request->getCookie(UserTokenService::ACCESS_TOKEN_COOKIE)),
        ) ?? [];
    }

    public function getType(): Fields\Type
    {
        return new Fields\Type(Fields\Type::TYPE_USER);
    }

    public function getAccountId(): Fields\AccountId
    {
        return new Fields\AccountId\UserAccountId(StrictCast::toString($this->auth['account_id'] ?? null));
    }

    public function getAccountEmail(): Fields\AccountEmail
    {
        return new Fields\AccountEmail(Cast::toStringOrNull($this->auth['account_email'] ?? null));
    }

    public function getAccountName(): Fields\AccountName
    {
        return new Fields\AccountName(StrictCast::toString($this->auth['account_name'] ?? ''));
    }

    public function getAccountStatusMasterId(): Fields\AccountStatusMasterId
    {
        return new Fields\AccountStatusMasterId(Cast::toStringOrNull($this->auth['account_status_master_id'] ?? null));
    }

    public function getAccountStatusMasterName(): Fields\AccountStatusMasterName
    {
        return new Fields\AccountStatusMasterName(Cast::toStringOrNull($this->auth['account_status_master_name'] ?? null));
    }

    public function getAccountStatusMasterCode(): Fields\AccountStatusMasterCode
    {
        return new Fields\AccountStatusMasterCode(Cast::toStringOrNull($this->auth['account_status_master_code'] ?? null));
    }

    public function getIsEmailVerified(): Fields\IsEmailVerified
    {
        return new Fields\IsEmailVerified(StrictCast::toBool($this->auth['is_email_verified'] ?? false));
    }

    public function getPasswordChangedAt(): Fields\PasswordChangedAt
    {
        return new Fields\PasswordChangedAt(Cast::toStringOrNull($this->auth['password_changed_at'] ?? null));
    }

    public function getPasswordExpiresAt(): Fields\PasswordExpiresAt
    {
        return new Fields\PasswordExpiresAt(Cast::toStringOrNull($this->auth['password_expires_at'] ?? null));
    }

    public function getCreated(): Fields\Created
    {
        return new Fields\Created(Cast::toStringOrNull($this->auth['created'] ?? null));
    }

    public function getModified(): Fields\Modified
    {
        return new Fields\Modified(Cast::toStringOrNull($this->auth['modified'] ?? null));
    }

    public function getLogined(): Fields\Logined
    {
        return new Fields\Logined(Cast::toStringOrNull($this->auth['logined'] ?? null));
    }

    public function isImpersonatorLogin(): bool
    {
        return false;
    }

    public function impersonatorAccount(): ?self
    {
        return null;
    }
}
