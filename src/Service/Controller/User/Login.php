<?php
declare(strict_types=1);

namespace App\Service\Controller\User;

use App\Domain\Admin\AdminAccounts\ValueObject\AccountStatusMasterCode;
use App\Domain\Log\LoginLogs\Entity\LoginLog as LoginLogEntity;
use App\Domain\Log\LoginLogs\ValueObject as LoginLogVo;
use App\Exception\AuthException;
use App\Infrastructure\Persistence\Cake\Log\LoginLogs\LoginLogsRepository;
use App\Infrastructure\Persistence\Cake\User\RefreshTokensRepository;
use App\Infrastructure\Persistence\Cake\User\UserAccountsRepository;
use App\Lib\UUID\UUID;
use App\Model\Entity\User\UserAccount;
use App\Security\Auth\UserTokenService;
use App\Security\Input\Cast;
use App\Service\Controller\Shared\ServiceInterface;
use App\Service\Controller\Shared\ServiceTrait;
use Authentication\PasswordHasher\DefaultPasswordHasher;

final class Login implements ServiceInterface
{
    use ServiceTrait;

    /**
     * @var \App\Model\Entity\User\UserAccount
     */
    private UserAccount $accountEntity;

    /**
     * @var string
     */
    private string $login_id;

    /**
     * @var string
     */
    private string $password;

    /**
     * @var string
     */
    private string $account_id;

    /**
     * @var array<int, string>
     */
    private array $setCookieHeaders = [];

    /**
     * @param string $login_id
     * @param string $password
     * @return self
     */
    public function login(string $login_id, string $password): self
    {
        $this->login_id = $login_id;
        $this->password = $password;

        try {
            return $this
                ->checkLoginFailureCount()
                ->loadAccountEntity()
                ->verifyPassword()
                ->checkPasswordExpiresAt()
                ->checkAccountStatus()
                ->createTokens();
        } catch (AuthException $e) {
            $this->recordLoginFailure($e);

            throw $e;
        }
    }

    /**
     * @return self
     */
    private function checkLoginFailureCount(): self
    {
        if (!(new LoginLogsRepository())->checkFailureLoginLimit(LoginLogVo\LoginId::fromString($this->login_id))) {
            throw new AuthException(
                __('ログイン失敗回数が上限に達したため、アカウントがロックされました。しばらくしてから再度お試しください。'),
                AuthException::LOGIN_FAIL_COUNT_OVER,
            );
        }

        return $this;
    }

    /**
     * @return self
     */
    private function loadAccountEntity(): self
    {
        $this->accountEntity = (new UserAccountsRepository())->findByEmail($this->login_id)
            ?? throw new AuthException(
                __('ログインIDまたはパスワードが違います。'),
                AuthException::LOGIN_ID_NOT_FOUND,
            );

        return $this;
    }

    /**
     * @return self
     */
    private function verifyPassword(): self
    {
        if (!(new DefaultPasswordHasher())->check($this->password, (string)$this->accountEntity->password)) {
            throw new AuthException(
                __('ログインIDまたはパスワードが違います。'),
                AuthException::INVALID_PASSWORD,
            );
        }

        return $this;
    }

    /**
     * @return self
     */
    private function checkPasswordExpiresAt(): self
    {
        if ($this->accountEntity->password_expires_at->getTimestamp() < $this->datetime->getTimestamp()) {
            throw new AuthException(
                __('パスワードの有効期限が切れています。'),
                AuthException::PASSWORD_EXPIRED,
            );
        }

        return $this;
    }

    /**
     * @return self
     */
    private function checkAccountStatus(): self
    {
        return match ((string)$this->accountEntity->account_status_master->code) {
            AccountStatusMasterCode::ACTIVE, AccountStatusMasterCode::PENDING => $this,
            AccountStatusMasterCode::SUSPENDED => throw new AuthException(
                __('アカウントが無効です。'),
                AuthException::ACCOUNT_SUSPENDED,
            ),
            AccountStatusMasterCode::LOCKED => throw new AuthException(
                __('アカウントが無効です。'),
                AuthException::ACCOUNT_LOCKED,
            ),
            AccountStatusMasterCode::DELETED => throw new AuthException(
                __('アカウントが無効です。'),
                AuthException::ACCOUNT_DELETED,
            ),
            default => throw new AuthException(
                __('アカウントが無効です。'),
                AuthException::AUTHENTICATION_FAILED,
            ),
        };
    }

    /**
     * @return self
     */
    private function createTokens(): self
    {
        $this->account_id = (string)$this->accountEntity->id;
        $tokenSet = (new UserTokenService())->createTokenSet($this->accountEntity, $this->datetime);
        (new RefreshTokensRepository())->create(
            id: $tokenSet['refresh_token_id'],
            userAccountId: $this->account_id,
            expiresAt: $tokenSet['refresh_token_expires_at'],
            now: $this->datetime,
        );
        $this->setCookieHeaders = $tokenSet['set_cookie_headers'];

        return $this;
    }

    /**
     * @return self
     */
    public function recordLoginSuccess(): self
    {
        (new LoginLogsRepository())->create(new LoginLogEntity(
            id: UUID::uuid7(),
            login_id: $this->login_id,
            login_actor_type: LoginLogVo\LoginActorType::USER,
            account_id: isset($this->accountEntity) ? (string)$this->accountEntity->id : null,
            impersonator_account_id: null,
            login_result: LoginLogVo\LoginResult::SUCCESS,
            ip_address: $this->request->clientIp(),
            user_agent: $this->request->getHeaderLine('User-Agent'),
            failure_reason_code: null,
            logged_in_at: $this->datetime->format('Y-m-d\TH:i:s'),
            created: $this->datetime->format('Y-m-d\TH:i:s'),
        ));

        return $this;
    }

    /**
     * @return array<string, string>|string
     */
    public function getRedirect(): string|array
    {
        $redirect = Cast::toStringOrNull($this->request->getQuery('redirect')) ?? '';
        if (preg_match('/^\/v1\/us\/\d+\/.*$/', $redirect)) {
            /** @var string */
            return preg_replace('/^(\/v1\/us)\/\d+\/(.*)$/', '$1/' . $this->account_id . '/$2', $redirect);
        }

        return [
            'prefix' => 'User',
            'controller' => 'Top',
            'action' => 'index',
            'account_id' => $this->account_id,
        ];
    }

    /**
     * @return array<int, string>
     */
    public function getSetCookieHeaders(): array
    {
        return $this->setCookieHeaders;
    }

    /**
     * @param \App\Exception\AuthException $e
     * @return self
     */
    public function recordLoginFailure(AuthException $e): self
    {
        if ($e->getFailureReasonCode() === AuthException::LOGIN_FAIL_COUNT_OVER) {
            return $this;
        }

        (new LoginLogsRepository())->create(new LoginLogEntity(
            id: UUID::uuid7(),
            login_id: $this->login_id,
            login_actor_type: LoginLogVo\LoginActorType::USER,
            account_id: isset($this->accountEntity) ? (string)$this->accountEntity->id : null,
            impersonator_account_id: null,
            login_result: LoginLogVo\LoginResult::FAILURE,
            ip_address: $this->request->clientIp(),
            user_agent: $this->request->getHeaderLine('User-Agent'),
            failure_reason_code: $e->getFailureReasonCode(),
            logged_in_at: $this->datetime->format('Y-m-d\TH:i:s'),
            created: $this->datetime->format('Y-m-d\TH:i:s'),
        ));

        return $this;
    }
}
