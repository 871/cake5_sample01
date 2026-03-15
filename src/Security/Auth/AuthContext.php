<?php
declare(strict_types=1);

namespace App\Security\Auth;

use App\Security\Auth\AuthContext\Fields;

interface AuthContext
{
    public const TYPE_ANONYMOUS = 'anonymous';
    public const TYPE_CUSTMER = 'custmer';
    public const TYPE_USER = 'user';
    public const TYPE_ADMIN = 'admin';

    /**
     * 認証コンテキストのタイプを取得する
     *
     * @return \App\Security\Auth\AuthContext\Fields\Type
     */
    public function getType(): Fields\Type;

    /**
     * 認証アカウントIDを取得する
     *
     * @return \App\Security\Auth\AuthContext\Fields\AccountId
     */
    public function getAccountId(): Fields\AccountId;


    /*
        login_code

        email VARCHAR(255) NOT NULL COMMENT 'ログインメールアドレス',
        name VARCHAR(100) NOT NULL COMMENT '表示名',
        account_status_master_code
        account_status_master_name
        is_email_verified INT NOT NULL DEFAULT 0 COMMENT 'メール確認済フラグ',
        password_changed_at DATETIME NOT NULL COMMENT 'パスワード最終変更日時',
        password_expires_at DATETIME NOT NULL COMMENT 'パスワード有効期限',
        
        created DATETIME NOT NULL COMMENT '作成日時',
        modified DATETIME NOT NULL COMMENT '更新日時',

    */

}
