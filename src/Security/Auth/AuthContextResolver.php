<?php
declare(strict_types=1);

namespace App\Security\Auth;

use App\Security\Auth\AuthContext\Fields;
use Cake\Http\ServerRequest;

final class AuthContextResolver
{
    /**
     * @param \Cake\Http\ServerRequest $request
     */
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

        return match (preg_replace('/^\/v1\/([^\/]+)\/.*$/', '$1', $ins->request->getPath())) {

            // 未認証の場合は匿名のAuthContextを返す
            default => $ins->createAuthContextForAnonymous(),
        };
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
            /**
             * @param \App\Security\Auth\AuthContext\Fields\Type $type
             * @param \App\Security\Auth\AuthContext\Fields\AccountId $accountId
             */
            public function __construct(
                private readonly Fields\Type $type,
                private readonly Fields\AccountId $accountId,
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
             * @return \App\Security\Auth\AuthContext\Fields\AccountId
             */
            public function getAccountId(): Fields\AccountId
            {
                return $this->accountId;
            }
        };
    }
}
