<?php
declare(strict_types=1);

namespace App\Middleware\Admin;

use App\Security\Input\StrictCast;
use Cake\Http\Response;
use Cake\ORM\Locator\LocatorAwareTrait;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class AdminGrantMiddleware implements MiddlewareInterface
{
    use LocatorAwareTrait;

    public const ACCOUNT_TYPE = 'ADMIN';

    /**
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Server\RequestHandlerInterface $handler
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        /** @var \Cake\Http\ServerRequest $request */
        $accountId = StrictCast::toString($request->getParam('account_id'));
        if (
            $accountId !== ''
            && $this->hasPageAccessPermission($request, $accountId)
        ) {
            // Memo: 認証情報は別のミドルウェアで検証するため、ここではアクセス権限の有無のみを判定する
            return $handler->handle($request);
        }

        $request->getSession()->write('Flash.flash', [[
            'message' => 'アクセス権限がありません。',
            'key' => 'flash',
            'element' => 'Flash/error',
            'params' => [],
        ]]);

        return (new Response())->withLocation('/v1/ad/' . rawurlencode($accountId));
    }

    /**
     * ページアクセス権限を判定する
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param string $accountId
     * @return bool
     */
    private function hasPageAccessPermission(ServerRequestInterface $request, string $accountId): bool
    {
        // TODO 未実装
        // リクエスト情報から権限コードを作成
        //  'PageAccess.' . コントローラのプレフィックス
        // 例： App\Controller\Admin\AdminAccount\EditController => 'PageAccess.Admin.AdminAccount'
        // 例： App\Controller\Admin\AdminGrant\Role => 'PageAccess.Admin.AdminGrant.Role'

        // 権限コードが存在しない場合はアクセス可
        // 権限コードが存在し、管理者アカウントIDと紐づいている場合はアクセス可
        // SystemAdministrator権限があればアクセス可
        // それ以外はアクセス不可

        return true;
    }
}
