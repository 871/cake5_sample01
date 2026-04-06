<?php
declare(strict_types=1);

namespace App\Middleware\Admin;

use App\Domain\Log\PageAccessLogs\Entity\PageAccessLog;
use App\Infrastructure\Persistence\Cake\Log\PageAccessLogs\PageAccessLogsRepository;
use App\Security\Input\StrictCast;
use Cake\Log\Log;
use DateTimeImmutable;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class PageAccessLogMiddleware implements MiddlewareInterface
{
    /**
     * @param \App\Infrastructure\Persistence\Cake\Log\PageAccessLogs\PageAccessLogsRepository $repository
     */
    public function __construct(
        private readonly PageAccessLogsRepository $repository = new PageAccessLogsRepository(),
    ) {
    }

    /**
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Server\RequestHandlerInterface $handler
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);

        try {
            /** @var \Cake\Http\ServerRequest $request */
            $account_id = StrictCast::toString($request->getParam('account_id'));

            $uri = $request->getUri();
            $path = $uri->getPath();
            $query_string = $uri->getQuery() !== '' ? $uri->getQuery() : null;

            $parsedBody = $request->getParsedBody();
            $post_keys = null;
            if (is_array($parsedBody) && $parsedBody !== []) {
                $post_keys = json_encode(array_keys($parsedBody));
            }

            $prefix = StrictCast::toString($request->getParam('prefix'));
            $controller = StrictCast::toString($request->getParam('controller'));
            $action = StrictCast::toString($request->getParam('action'));
            $route_name = null;
            if ($controller !== '' && $action !== '') {
                $route_name = ($prefix !== '' ? $prefix . '/' : '') . $controller . '::' . $action;
            }

            $referer = $request->getHeaderLine('Referer') !== '' ? $request->getHeaderLine('Referer') : null;
            $ip_address = $request->clientIp() !== '' ? $request->clientIp() : null;
            $user_agent = $request->getHeaderLine('User-Agent') !== '' ? $request->getHeaderLine('User-Agent') : null;

            $accessed = (new DateTimeImmutable())->format('Y-m-d\TH:i:s.u');

            $entity = new PageAccessLog(
                id: null,
                accessed: $accessed,
                account_type: 'ADMIN',
                account_id: $account_id,
                method: $request->getMethod(),
                path: $path,
                query_string: $query_string,
                post_keys: $post_keys,
                route_name: $route_name,
                referer: $referer,
                ip_address: $ip_address,
                user_agent: $user_agent,
                created: null,
            );

            $this->repository->create($entity);
        } catch (\Throwable $e) {
            // ログ記録の失敗はリクエストの処理に影響させない
            Log::error(sprintf(
                'PageAccessLogMiddleware: failed to create log. %s: %s',
                get_class($e),
                $e->getMessage(),
            ));
        }

        return $response;
    }
}
