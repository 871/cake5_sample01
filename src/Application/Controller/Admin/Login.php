<?php
declare(strict_types=1);

namespace App\Application\Controller\Admin;

use App\Application\Controller\Shared\ServiceInterface;
use App\Application\Controller\Shared\ServiceTrait;
use App\Domain\Admin\AdminAccounts\Entity\AdminAccount as AccountEntity;
use App\Domain\Admin\AdminAccounts\ValueObject as Vo;
use App\Domain\Log\LoginLogs\Entity\LoginLog as LoginLogEntity;
use App\Domain\Log\LoginLogs\ValueObject as LoginLogVo;
use App\Exception\AuthException;
use App\Infrastructure\Persistence\Cake\Admin\AdminAccountsRepository;
use App\Infrastructure\Persistence\Cake\Admin\AdminAccountsRepository\Mapper as AdminAccountMapper;
use App\Infrastructure\Persistence\Cake\Log\LoginLogs\LoginLogsRepository;
use App\Lib\UUID\UUID;
use App\Security\Auth\AuthContext\Fields\Type;
use App\Security\Auth\AuthSession;
use App\Security\Input\Cast;
use Authentication\PasswordHasher\DefaultPasswordHasher;

final class Login implements ServiceInterface
{
    use ServiceTrait;

    /**
     * @var \App\Domain\Admin\AdminAccounts\Entity\AdminAccount
     */
    private AccountEntity $accountEntity;

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
     * @param string $login_id
     * @param string $password
     * @return self
     */
    public function login(
        string $login_id,
        string $password,
    ): self {
        $this->login_id = $login_id;
        $this->password = $password;

        try {
            return $this
                ->checkLoginFailureCount() // ログイン失敗回数超過チェック
                ->loadAccountEntity() // 管理者情報取得
                ->verifyPassword() // PW照合
                ->checkPasswordExpiresAt() // 有効期限
                ->checkAccountStatus() // ステータス判定
                ->createLoginSession(); // ログインセッション作成
        } catch (AuthException $e) {
            $this->recordLoginFailure($e); // ログイン失敗ログ出力

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
        $this->accountEntity = (new AdminAccountsRepository($this->datetime))
            ->findByEmail(Vo\Email::fromString($this->login_id))
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
        $check = (new DefaultPasswordHasher())
            ->check($this->password, $this->accountEntity->password()->toString());

        if (!$check) {
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
        $expiresTimestamp = $this->accountEntity
            ->passwordExpiresAt()
            ->toDateTimeOrNull()
            ?->getTimestamp() ?? 0;

        $nowTimestamp = $this->datetime->getTimestamp();
        if ($expiresTimestamp < $nowTimestamp) {
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
        return match ($this->accountEntity->accountStatusMasterCode()->toString()) {
            Vo\AccountStatusMasterCode::ACTIVE => $this,
            Vo\AccountStatusMasterCode::PENDING => $this,
            Vo\AccountStatusMasterCode::SUSPENDED => throw new AuthException(
                __('アカウントが無効です。'),
                AuthException::ACCOUNT_SUSPENDED,
            ),
            Vo\AccountStatusMasterCode::LOCKED => throw new AuthException(
                __('アカウントが無効です。'),
                AuthException::ACCOUNT_LOCKED,
            ),
            Vo\AccountStatusMasterCode::DELETED => throw new AuthException(
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
    private function createLoginSession(): self
    {
        $this->account_id = $this->accountEntity->id()->toString();
        $authSession = new AuthSession(
            request: $this->request,
            type: Type::TYPE_ADMIN,
            account_id: $this->account_id,
        );
        $authSession->write(
            (new AdminAccountMapper())->toAuthSessionParams($this->accountEntity, $this->datetime),
        );

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
            login_actor_type: LoginLogVo\LoginActorType::ADMIN,
            account_id: isset($this->accountEntity) ? $this->accountEntity->id()->toString() : null, // ログインIDをaccount_idとして記録
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
        // ログイン前のURLが特定のパターンにマッチする場合は、そのURLにリダイレクト。それ以外は管理画面トップへリダイレクト。
        // 例）/v1/ad/{account_id}/... → /v1/ad/{ログインしたアカウントのaccount_id}/...
        $redirect = Cast::toStringOrNull($this->request->getQuery('redirect')) ?? '';
        if (preg_match('/^\/v1\/ad\/\d+\/.*$/', $redirect)) {
            /** @var string */
            return preg_replace('/^(\/v1\/ad)\/\d+\/(.*)$/', '$1/' . $this->account_id . '/$2', $redirect);
        }

        return [
            'prefix' => 'Admin',
            'controller' => 'Top',
            'action' => 'index',
            'account_id' => $this->account_id,
        ];
    }

    /**
     * @param \App\Exception\AuthException $e
     */
    public function recordLoginFailure(AuthException $e): self
    {
        if ($e->getFailureReasonCode() === AuthException::LOGIN_FAIL_COUNT_OVER) {
            return $this; // ログイン失敗回数超過の場合は、ログイン失敗ログの記録は行わない（すでにログイン失敗回数超過の状態であるため）
        }

        (new LoginLogsRepository())->create(new LoginLogEntity(
            id: UUID::uuid7(),
            login_id: $this->login_id,
            login_actor_type: LoginLogVo\LoginActorType::ADMIN,
            account_id: isset($this->accountEntity) ? $this->accountEntity->id()->toString() : null,
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
