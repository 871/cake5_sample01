<?php
declare(strict_types=1);

namespace App\Security\Auth;

use App\Security\Auth\AuthContext\Fields;
use Cake\Http\ServerRequest;

final class AuthContextResolver
{
    private function __construct(
        private readonly ServerRequest $request,
    ) {
    }

    /**
     * @return \App\Security\Auth\AuthContext
     */
    public static function resolve(ServerRequest $request): AuthContext
    {
        $ins = new self($request);

        // 未認証の場合は匿名のAuthContextを返す
        return $ins->createAuthContextForAnonymous();
    }

    /**
     * 匿名のAuthContextを生成する
     *
     * @return \App\Security\Auth\AuthContext
     */
    private function createAuthContextForAnonymous(): AuthContext
    {
        return new class (
            type: new Fields\Type(AuthContext::TYPE_ANONYMOUS),
            accountId: new Fields\AccountId(null)
        ) implements AuthContext {
            public function __construct(
                private readonly Fields\Type $type,
                private readonly Fields\AccountId $accountId,
            ) {
                // 処理なし
            }

            public function getType(): Fields\Type
            {
                return $this->type;
            }

            public function getAccountId(): Fields\AccountId
            {
                return $this->accountId;
            }
        };
    }
}
