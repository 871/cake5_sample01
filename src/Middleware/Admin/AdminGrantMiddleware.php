<?php
declare(strict_types=1);

namespace App\Middleware\Admin;

use App\Domain\Admin\AdminGrant\ValueObject\AdminAccountId;
use App\Domain\Admin\AdminGrant\ValueObject\Code;
use App\Infrastructure\Persistence\Cake\Admin\AdminGrant\AdminAccountGrantRepository;
use App\Model\Table\Grant\GrantPermissionsTable;
use App\Security\Input\StrictCast;
use Cake\Http\Response;
use Cake\ORM\Locator\LocatorAwareTrait;
use DateTimeImmutable;
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
    public const PERMISSION_CODE_SYSTEM_ADMINISTRATOR = 'SystemAdministrator';

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
        // 例： App\Controller\Admin\AdminAccount\EditController => 'PageAccess.Admin.AdminAccount'
        // 例： App\Controller\Admin\AdminGrant\Role => 'PageAccess.Admin.AdminGrant.Role'
        /** @var \Cake\Http\ServerRequest $request */
        $prefix = StrictCast::toString($request->getParam('prefix'));
        if ($prefix === '') {
            return true;
        }
        $permissionCode = 'PageAccess.' . str_replace('/', '.', $prefix);

        // grant_permissions テーブルで権限コードの存在確認
        /** @var \App\Model\Table\Grant\GrantPermissionsTable $table */
        $table = $this->fetchTable(GrantPermissionsTable::class);
        $permission = $table->find()
            ->select(['id'])
            ->where([
                'account_type' => self::ACCOUNT_TYPE,
                'code' => $permissionCode,
                'is_active' => 1,
            ])
            ->first();

        // 権限コードが存在しない場合はアクセス可
        if ($permission === null) {
            return true;
        }

        // 管理者アカウントの権限情報を取得
        $adminAccountGrant = (new AdminAccountGrantRepository(new DateTimeImmutable()))
            ->detail(new AdminAccountId($accountId));

        // SystemAdministrator権限があればアクセス可
        if ($adminAccountGrant->hasPermissionCode(new Code(self::PERMISSION_CODE_SYSTEM_ADMINISTRATOR))) {
            return true;
        }

        // 権限コードが存在し、管理者アカウントIDと紐づいている場合はアクセス可
        return $adminAccountGrant->hasPermissionCode(new Code($permissionCode));
    }
}
