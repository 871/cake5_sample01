<?php
declare(strict_types=1);

namespace App\Middleware\Admin;

use App\Security\Auth\AuthContext\Fields\Type;
use App\Security\Auth\AuthSession;
use App\Security\Input\StrictCast;
use Cake\Http\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class AdminAuthMiddleware implements MiddlewareInterface
{
    /**
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Server\RequestHandlerInterface $handler
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        /** @var \Cake\Http\ServerRequest $request */
        $account_id = StrictCast::toString($request->getParam('account_id'));

        $authSession = new AuthSession(
            request: $request,
            type: Type::TYPE_ADMIN,
            account_id: $account_id,
        );

        if (!$authSession->check()) {
            $uri = $request->getUri();
            $currentPath = $uri->getPath();
            $currentQuery = $uri->getQuery();
            $currentUrl = $currentQuery !== '' ? $currentPath . '?' . $currentQuery : $currentPath;

            $request->getSession()->write('Flash.flash', [
                [
                    'message' => 'ログアウトしました。',
                    'key' => 'flash',
                    'element' => 'Flash/error',
                    'params' => [],
                ],
            ]);

            return (new Response())->withLocation('/v1/ad/login?redirect=' . urlencode($currentUrl));
        }

        return $handler->handle($request);
    }
}
