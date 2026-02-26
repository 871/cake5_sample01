<?php declare(strict_types=1);

namespace App\Security\Auth;

final class AuthContextResolver
{
    private function __construct(
        private readonly \Cake\Http\ServerRequest $request
    ) {}

    /**
     * 
     * @return \App\Security\Auth\AuthContext
     */
    public static function resolve(\Cake\Http\ServerRequest $request) : AuthContext
    {
        $ins = new self($request);
        
        // 未認証の場合は匿名のAuthContextを返す
        return $ins->createAuthContextForAnonymous();
    }

    /**
     * 匿名のAuthContextを生成する
     * @return \App\Security\Auth\AuthContext
     */   
    private function createAuthContextForAnonymous() : AuthContext
    {
        return new class() implements AuthContext {
            // 匿名ユーザーの情報を保持するプロパティやメソッドを定義することができます
        };
    }
}