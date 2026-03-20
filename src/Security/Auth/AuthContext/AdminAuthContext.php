<?php
declare(strict_types=1);

namespace App\Security\Auth\AuthContext;

use App\Security\Auth\AuthContext;
use App\Security\Auth\AuthContext\Fields;
use BadMethodCallException;

final class AdminAuthContext implements AuthContext
{
    /**
     * @param \App\Security\Auth\AuthContext\Fields\Type $type
     * @param \App\Security\Auth\AuthContext\Fields\AccountId\AdminAccountId $accountId
     * @param \App\Security\Auth\AuthContext\Fields\AccountEmail\AdminAccountEmail $accountEmail
     * @param \App\Security\Auth\AuthContext\Fields\AccountName\AdminAccountName $accountName
     * @param \App\Security\Auth\AuthContext\Fields\AccountStatusMasterId\AdminAccountStatusMasterId $accountStatusMasterId
     * @param \App\Security\Auth\AuthContext\Fields\AccountStatusMasterName\AdminAccountStatusMasterName $accountStatusMasterName
     * @param \App\Security\Auth\AuthContext\Fields\AccountStatusMasterCode\AdminAccountStatusMasterCode $accountStatusMasterCode
     * @param \App\Security\Auth\AuthContext\Fields\IsEmailVerified $isEmailVerified
     * @param \App\Security\Auth\AuthContext\Fields\PasswordChangedAt $passwordChangedAt
     * @param \App\Security\Auth\AuthContext\Fields\PasswordExpiresAt $passwordExpiresAt
     * @param \App\Security\Auth\AuthContext\Fields\Created $created
     * @param \App\Security\Auth\AuthContext\Fields\Modified $modified
     * @param \App\Security\Auth\AuthContext\Fields\Logined $logined
     */
    public function __construct(
        private readonly Fields\Type $type,
        private readonly Fields\AccountId\AdminAccountId $accountId,
        private readonly Fields\AccountEmail\AdminAccountEmail $accountEmail,
        private readonly Fields\AccountName\AdminAccountName $accountName,
        private readonly Fields\AccountStatusMasterId\AdminAccountStatusMasterId $accountStatusMasterId,
        private readonly Fields\AccountStatusMasterName\AdminAccountStatusMasterName $accountStatusMasterName,
        private readonly Fields\AccountStatusMasterCode\AdminAccountStatusMasterCode $accountStatusMasterCode,
        private readonly Fields\IsEmailVerified $isEmailVerified,
        private readonly Fields\PasswordChangedAt $passwordChangedAt,
        private readonly Fields\PasswordExpiresAt $passwordExpiresAt,
        private readonly Fields\Created $created,
        private readonly Fields\Modified $modified,
        private readonly Fields\Logined $logined,
    ) {
        // 処理なし
    }

    /**
     * @return \App\Security\Auth\AuthContext\Fields\Type
     */
    public function getType(): Fields\Type
    {
        return $this->type;
    }

    /**
     * @return ?\App\Security\Auth\AuthContext\Fields\AccountId
     */
    public function getAccountId(): ?Fields\AccountId
    {
        return $this->accountId;
    }

    /**
     * 認証アカウントメールアドレス
     *
     * @return ?\App\Security\Auth\AuthContext\Fields\AccountEmail
     */
    public function getAccountEmail(): ?Fields\AccountEmail
    {
        return $this->accountEmail;
    }

    /**
     * 認証アカウント表示名
     *
     * @return \App\Security\Auth\AuthContext\Fields\AccountName
     */
    public function getAccountName(): Fields\AccountName
    {
        return $this->accountName;
    }

    /**
     * 認証アカウントステータスマスターID
     *
     * @return ?\App\Security\Auth\AuthContext\Fields\AccountStatusMasterId
     */
    public function getAccountStatusMasterId(): ?Fields\AccountStatusMasterId
    {
        return $this->accountStatusMasterId;
    }

    /**
     * 認証アカウントステータスマスターネーム
     *
     * @return ?\App\Security\Auth\AuthContext\Fields\AccountStatusMasterName
     */
    public function getAccountStatusMasterName(): ?Fields\AccountStatusMasterName
    {
        return $this->accountStatusMasterName;
    }

    /**
     * 認証アカウントステータスマスターコード
     *
     * @return ?\App\Security\Auth\AuthContext\Fields\AccountStatusMasterCode
     */
    public function getAccountStatusMasterCode(): ?Fields\AccountStatusMasterCode
    {
        return $this->accountStatusMasterCode;
    }

    /**
     * 認証アカウントメール確認済フラグ
     *
     * @return ?\App\Security\Auth\AuthContext\Fields\IsEmailVerified
     */
    public function getIsEmailVerified(): ?Fields\IsEmailVerified
    {
        return $this->isEmailVerified;
    }

    /**
     * 認証アカウントパスワード最終変更日時
     *
     * @return ?\App\Security\Auth\AuthContext\Fields\PasswordChangedAt
     */
    public function getPasswordChangedAt(): ?Fields\PasswordChangedAt
    {
        return $this->passwordChangedAt;
    }

    /**
     * 認証アカウントパスワード有効期限
     *
     * @return ?\App\Security\Auth\AuthContext\Fields\PasswordExpiresAt
     */    
    public function getPasswordExpiresAt(): ?Fields\PasswordExpiresAt
    {
        return $this->passwordExpiresAt;
    }

    /**
     * 認証アカウント作成日時
     *
     * @return ?\App\Security\Auth\AuthContext\Fields\Created
     */
    public function getCreated(): ?Fields\Created
    {
        return $this->created;
    }

    /**
     * 認証アカウント更新日時
     *
     * @return ?\App\Security\Auth\AuthContext\Fields\Modified
     */
    public function getModified(): ?Fields\Modified
    {
        return $this->modified;
    }

    /**
     * ログイン日時
     *
     * @return \App\Security\Auth\AuthContext\Fields\Logined
     */
    public function getLogined(): Fields\Logined
    {
        return $this->logined;
    }
}