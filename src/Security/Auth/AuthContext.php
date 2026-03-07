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
}
