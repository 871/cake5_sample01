<?php declare(strict_types=1);

namespace App\Security\Auth;

class AuthContextResolver
{
    /**
     * 
     * @var \Cake\Http\ServerRequest
     */
    private \Cake\Http\ServerRequest $request;

    private function __construct(\Cake\Http\ServerRequest $request)
    {
        $this->request = $request;
    }

    /**
     * 
     * @return self
     */
    public static function getInstance(\Cake\Http\ServerRequest $request) : self
    {
        static $ins = null;
        if ($ins === null) {
            $ins = new self($request);
        }

        return $ins;
    }

    /**
     * @return \App\Security\Auth\AuthContext
     */
    public function resolve() : AuthContext
    {
        
        // 未認証の場合は匿名のAuthContextを返す
        return $this->createAuthContextForAnonymous();
    }

    /**
     * 匿名のAuthContextを生成する
     * @return \App\Security\Auth\AuthContext
     */   
    private function createAuthContextForAnonymous() : AuthContext
    {
        return new class implements AuthContext {
            // 匿名ユーザーの情報を保持するプロパティやメソッドを定義することができます
        };
    }
}