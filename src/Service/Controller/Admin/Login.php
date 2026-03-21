<?php
declare(strict_types=1);

namespace App\Service\Controller\Admin;

use App\Infrastructure\Persistence\Cake\Admin\AdminAccountsRepository;
use App\Domain\Admin\AdminAccounts\ValueObject as Vo;
use App\Security\Auth\AuthContext;
use App\Service\Controller\Shared\ServiceInterface;
use App\Service\Controller\Shared\ServiceTrait;
use App\Domain\Admin\AdminAccounts\Entity\AdminAccount as AccountEntity;
use Cake\ORM\Locator\LocatorAwareTrait;
use Cake\Http\ServerRequest;
use DateTimeInterface;
use App\Exception\AuthException;
use Authentication\PasswordHasher\DefaultPasswordHasher;


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
        $this->accountEntity;
        // TODO: パスワード有効期限の判定
        return $this;
    }

    /**
     * @return self
     */
    private function checkAccountStatus(): self
    {
        $this->accountEntity;
        // TODO: アカウントステータスの判定
        
        return $this;
    }

    /**
     * @return self
     */
    private function createLoginSession(): self
    {
        $this->accountEntity;
        // TODO: ログインセッションの作成
        
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
