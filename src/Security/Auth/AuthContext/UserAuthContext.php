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
            /** @var array<string, string> $auth */
            $this->auth = array_map(static fn(mixed $value): string => (string)$value, $auth);

            return;
        }

        $this->auth = (new UserTokenService())->readAccessToken(
            Cast::toStringOrNull($this->request->getCookie(UserTokenService::ACCESS_TOKEN_COOKIE)),
        ) ?? [];
    }

    /**
     * @return \App\Security\Auth\AuthContext\Fields\Type
     */
    public function getType(): Fields\Type
    {
        return new Fields\Type(Fields\Type::TYPE_USER);
    }

    /**
     * @return \App\Security\Auth\AuthContext\Fields\AccountId
     */
    public function getAccountId(): Fields\AccountId
    {
        return new Fields\AccountId\UserAccountId(StrictCast::toString($this->auth['account_id'] ?? null));
    }

    /**
     * @return \App\Security\Auth\AuthContext\Fields\AccountEmail
     */
    public function getAccountEmail(): Fields\AccountEmail
    {
        return new Fields\AccountEmail(Cast::toStringOrNull($this->auth['account_email'] ?? null));
    }

    /**
     * @return \App\Security\Auth\AuthContext\Fields\AccountName
     */
    public function getAccountName(): Fields\AccountName
    {
        return new Fields\AccountName(StrictCast::toString($this->auth['account_name'] ?? ''));
    }

    /**
     * @return \App\Security\Auth\AuthContext\Fields\AccountStatusMasterId
     */
    public function getAccountStatusMasterId(): Fields\AccountStatusMasterId
    {
        return new Fields\AccountStatusMasterId(Cast::toStringOrNull($this->auth['account_status_master_id'] ?? null));
    }

    /**
     * @return \App\Security\Auth\AuthContext\Fields\AccountStatusMasterName
     */
    public function getAccountStatusMasterName(): Fields\AccountStatusMasterName
    {
        return new Fields\AccountStatusMasterName(
            Cast::toStringOrNull($this->auth['account_status_master_name'] ?? null),
        );
    }

    /**
     * @return \App\Security\Auth\AuthContext\Fields\AccountStatusMasterCode
     */
    public function getAccountStatusMasterCode(): Fields\AccountStatusMasterCode
    {
        return new Fields\AccountStatusMasterCode(
            Cast::toStringOrNull($this->auth['account_status_master_code'] ?? null),
        );
    }

    /**
     * @return \App\Security\Auth\AuthContext\Fields\IsEmailVerified
     */
    public function getIsEmailVerified(): Fields\IsEmailVerified
    {
        return new Fields\IsEmailVerified(StrictCast::toBool($this->auth['is_email_verified'] ?? false));
    }

    /**
     * @return \App\Security\Auth\AuthContext\Fields\PasswordChangedAt
     */
    public function getPasswordChangedAt(): Fields\PasswordChangedAt
    {
        return new Fields\PasswordChangedAt(Cast::toStringOrNull($this->auth['password_changed_at'] ?? null));
    }

    /**
     * @return \App\Security\Auth\AuthContext\Fields\PasswordExpiresAt
     */
    public function getPasswordExpiresAt(): Fields\PasswordExpiresAt
    {
        return new Fields\PasswordExpiresAt(Cast::toStringOrNull($this->auth['password_expires_at'] ?? null));
    }

    /**
     * @return \App\Security\Auth\AuthContext\Fields\Created
     */
    public function getCreated(): Fields\Created
    {
        return new Fields\Created(Cast::toStringOrNull($this->auth['created'] ?? null));
    }

    /**
     * @return \App\Security\Auth\AuthContext\Fields\Modified
     */
    public function getModified(): Fields\Modified
    {
        return new Fields\Modified(Cast::toStringOrNull($this->auth['modified'] ?? null));
    }

    /**
     * @return \App\Security\Auth\AuthContext\Fields\Logined
     */
    public function getLogined(): Fields\Logined
    {
        return new Fields\Logined(Cast::toStringOrNull($this->auth['logined'] ?? null));
    }

    /**
     * @return bool
     */
    public function isImpersonatorLogin(): bool
    {
        return false;
    }

    /**
     * @return self|null
     */
    public function impersonatorAccount(): ?self
    {
        return null;
    }
}
