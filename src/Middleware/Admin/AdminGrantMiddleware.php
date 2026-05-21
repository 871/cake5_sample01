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

    const ACCOUNT_TYPE = 'ADMIN';

    /**
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Server\RequestHandlerInterface $handler
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        /** @var \Cake\Http\ServerRequest $request */
        $accountId = (string)$request->getParam('account_id');
        if (
            $accountId !== ''
            && $this->hasPageAccessPermission($request, $accountId)
        ) {
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

    private function hasPageAccessPermission(ServerRequestInterface $request, string $accountId): bool
    {
        // TODO 未実装 
        return true;
    }
}