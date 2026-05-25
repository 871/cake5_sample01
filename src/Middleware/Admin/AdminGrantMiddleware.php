<?php
declare(strict_types=1);

namespace App\Middleware\Admin;

use App\Domain\Admin\AdminGrant\ValueObject\AdminAccountId;
use App\Domain\Admin\AdminGrant\ValueObject\Code;
use App\Infrastructure\Persistence\Cake\Admin\AdminGrant\AdminGrantPermissionRepository;
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
     * SystemAdministrator 権限コード
     */
    private const PERMISSION_CODE_SYSTEM_ADMINISTRATOR = 'SystemAdministrator';

    /**
     * アクセス権限の判定を行う際の、許可するコントローラのプレフィックスリスト
     * 例： 'Admin' => App\Controller\Admin\XxxController, App\Controller\Admin\YyyController などを許可
     * 
     * @var array<string>
     */
    private array $allowPrefixList = [
        'Admin',
    ];

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
        // リクエスト情報から権限コードを作成
        //  'PageAccess.' . コントローラのプレフィックス（'/'を'.'に変換）
        // 例： App\Controller\Admin\AdminAccount\XxxController => 'PageAccess.Admin.AdminAccount'
        // 例： App\Controller\Admin\AdminGrant\Role\XxxController => 'PageAccess.Admin.AdminGrant.Role'
        /** @var \Cake\Http\ServerRequest $request */
        $prefix = StrictCast::toString($request->getParam('prefix'));
        if ($prefix === '' || in_array($prefix, $this->allowPrefixList, true)) {
            return true;
        }

        $adminGrantPermission = new AdminGrantPermissionRepository();

        return $adminGrantPermission->hasPermission(
            new Code('PageAccess.' . str_replace('/', '.', $prefix)),
            new AdminAccountId($accountId),
        ) || $adminGrantPermission->hasPermission(
            new Code(self::PERMISSION_CODE_SYSTEM_ADMINISTRATOR),
            new AdminAccountId($accountId),
        );
    }
}
