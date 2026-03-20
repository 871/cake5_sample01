<?php
declare(strict_types=1);

namespace App\Security\Auth\AuthContext;

use App\Security\Auth\AuthContext;
use App\Security\Auth\AuthContext\Fields;
use BadMethodCallException;

final class AnonymousAuthContext implements AuthContext
{
    /**
     * @param \App\Security\Auth\AuthContext\Fields\Type $type
     * @param \App\Security\Auth\AuthContext\Fields\AccountName $accountName
     */
    public function __construct(
        private readonly Fields\Type $type,
        private readonly Fields\AccountName $accountName,
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
        return null;
    }

    /**
     * 認証アカウントメールアドレス
     *
     * @return ?\App\Security\Auth\AuthContext\Fields\AccountEmail
     */
    public function getAccountEmail(): ?Fields\AccountEmail
    {
        return null;
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
        return null;
    }

    /**
     * 認証アカウントステータスマスターネーム
     *
     * @return ?\App\Security\Auth\AuthContext\Fields\AccountStatusMasterName
     */
    public function getAccountStatusMasterName(): ?Fields\AccountStatusMasterName
    {
        return null;
    }

    /**
     * 認証アカウントステータスマスターコード
     *
     * @return ?\App\Security\Auth\AuthContext\Fields\AccountStatusMasterCode
     */
    public function getAccountStatusMasterCode(): ?Fields\AccountStatusMasterCode
    {
        return null;
    }

    /**
     * 認証アカウントメール確認済フラグ
     *
     * @return ?\App\Security\Auth\AuthContext\Fields\IsEmailVerified
     */
    public function getIsEmailVerified(): ?Fields\IsEmailVerified
    {
        return null;
    }

    /**
     * 認証アカウントパスワード最終変更日時
     *
     * @return ?\App\Security\Auth\AuthContext\Fields\PasswordChangedAt
     */
    public function getPasswordChangedAt(): ?Fields\PasswordChangedAt
    {
        return null;
    }

    /**
     * 認証アカウントパスワード有効期限
     *
     * @return ?\App\Security\Auth\AuthContext\Fields\PasswordExpiresAt
     */    
    public function getPasswordExpiresAt(): ?Fields\PasswordExpiresAt
    {
        return null;
    }

    /**
     * 認証アカウント作成日時
     *
     * @return ?\App\Security\Auth\AuthContext\Fields\Created
     */
    public function getCreated(): ?Fields\Created
    {
        return null;
    }

    /**
     * 認証アカウント更新日時
     *
     * @return ?\App\Security\Auth\AuthContext\Fields\Modified
     */
    public function getModified(): ?Fields\Modified
    {
        return null;
    }

    /**
     * ログイン日時
     *
     * @return ?\App\Security\Auth\AuthContext\Fields\Logined
     */
    public function getLogined(): ?Fields\Logined
    {
        return null;
    }
}
