<?php
declare(strict_types=1);

namespace App\Service\Controller\Admin;

use App\Domain\Admin\AdminAccounts\Entity\AdminAccount as AccountEntity;
use App\Domain\Admin\AdminAccounts\ValueObject as Vo;
use App\Exception\AuthException;
use App\Infrastructure\Persistence\Cake\Admin\AdminAccountMapper;
use App\Infrastructure\Persistence\Cake\Admin\AdminAccountsRepository;
use App\Security\Auth\AuthContext;
use App\Security\Auth\AuthContext\Fields\Type;
use App\Security\Auth\AuthSession;
use App\Service\Controller\Shared\ServiceInterface;
use App\Service\Controller\Shared\ServiceTrait;
use Authentication\PasswordHasher\DefaultPasswordHasher;
use Cake\Http\ServerRequest;
use Cake\ORM\Locator\LocatorAwareTrait;
use DateTimeInterface;

final class Login implements ServiceInterface
{
    use ServiceTrait {
        ServiceTrait::__construct as private traitConstruct;
    }
    use LocatorAwareTrait;

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
     * @param \DateTimeInterface $datetime
     * @param \Cake\Http\ServerRequest $request
     * @param \App\Security\Auth\AuthContext $authContext
     */
    public function __construct(
        DateTimeInterface $datetime,
        ServerRequest $request,
        AuthContext $authContext,
    ) {
        $this->traitConstruct(
            datetime: $datetime,
            request: $request,
            authContext: $authContext,
        );
    }

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

        return $this
            ->loadAccountEntity() // 管理者情報取得
            ->verifyPassword() // PW照合
            ->checkPasswordExpiresAt() // 有効期限
            ->checkAccountStatus() // ステータス判定
            ->createLoginSession(); // ログインセッション作成
    }

    /**
     * @return self
     */
    private function loadAccountEntity(): self
    {
        $this->accountEntity = (new AdminAccountsRepository($this->datetime))
            ->findByEmail(Vo\Email::fromString($this->login_id))
            ?? throw new AuthException(__('ログインIDまたはパスワードが違います。'));

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
            throw new AuthException(__('ログインIDまたはパスワードが違います。'));
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
            throw new AuthException(__('パスワードの有効期限が切れています。'));
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
            Vo\AccountStatusMasterCode::SUSPENDED => throw new AuthException(__('アカウントが無効です。')),
            Vo\AccountStatusMasterCode::LOCKED => throw new AuthException(__('アカウントがロックされました。')),
            Vo\AccountStatusMasterCode::DELETED => throw new AuthException(__('アカウントが削除されました。')),
            default => throw new AuthException(__('アカウントが無効です。')),
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
     * @return string
     */
    public function getAccountId(): string
    {
        return $this->account_id;
    }
}
