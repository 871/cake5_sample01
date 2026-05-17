<?php
declare(strict_types=1);

namespace App\Middleware\Admin;

use App\Domain\Admin\AdminGrant\ValueObject\AdminAccountId;
use App\Domain\Admin\AdminGrant\ValueObject\GrantPermissionId;
use App\Infrastructure\Persistence\Cake\Admin\AdminGrantMapper;
use App\Infrastructure\Persistence\Cake\Admin\AdminGrantRepository;
use App\Model\Table\Grant\GrantPermissionsTable;
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

    /**
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Server\RequestHandlerInterface $handler
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        /** @var \Cake\Http\ServerRequest $request */
        $accountId = (string)$request->getParam('account_id');
        if ($accountId === '') {
            return $handler->handle($request);
        }

        $permissionId = $this->resolvePermissionId($request);
        if ($permissionId === null) {
            return $handler->handle($request);
        }

        $hasPermission = (new AdminGrantRepository(new DateTimeImmutable()))->hasPermission(
            new AdminAccountId($accountId),
            new GrantPermissionId((string)$permissionId),
        );

        if ($hasPermission) {
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
     * @param \Cake\Http\ServerRequest $request
     * @return int|null
     */
    private function resolvePermissionId(\Cake\Http\ServerRequest $request): ?int
    {
        $controller = (string)$request->getParam('controller');
        $action = (string)$request->getParam('action');
        $prefix = str_replace('/', '_', (string)$request->getParam('prefix'));

        if ($controller === '' || $action === '' || in_array($controller, ['Top', 'Error', 'Logout'], true)) {
            return null;
        }

        $code = strtoupper(trim($prefix . '_' . $controller . '_' . $action, '_'));

        /** @var \App\Model\Table\Grant\GrantPermissionsTable $table */
        $table = $this->fetchTable(GrantPermissionsTable::class);
        /** @var \App\Model\Entity\Grant\GrantPermission|null $permission */
        $permission = $table->find()
            ->select(['id'])
            ->where([
                'account_type' => AdminGrantMapper::ACCOUNT_TYPE,
                'code' => $code,
                'is_active' => 1,
            ])
            ->first();

        return $permission?->id;
    }
}
