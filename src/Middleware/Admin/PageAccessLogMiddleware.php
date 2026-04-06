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
            $uri = $request->getUri();
            $accessed = new DateTimeImmutable();
            $entity = new PageAccessLog(
                id: \App\Lib\UUID\UUID::uuid7(),
                accessed: $accessed->format('Y-m-d\TH:i:s.u'),
                account_type: \App\Domain\Log\PageAccessLogs\ValueObject\AccountType::ADMIN,
                account_id: StrictCast::toString($request->getParam('account_id')),
                method: $request->getMethod(),
                path: $uri->getPath(),
                query_string: $uri->getQuery() !== '' ? $uri->getQuery() : null,
                post_keys: (function() use ($request) {
                    $parsedBody = $request->getParsedBody();

                    return is_array($parsedBody) && $parsedBody !== []
                        ? json_encode(array_keys($parsedBody))
                        : null;
                })(),
                route_name: (function() use ($request) {
                    $prefix = StrictCast::toString($request->getParam('prefix'));
                    $controller = StrictCast::toString($request->getParam('controller'));
                    $action = StrictCast::toString($request->getParam('action'));

                    if ($controller === '' || $action === '') {
                        return null;
                    }

                    return 'App\\Controller\\' 
                        . ($prefix !== '' ? $prefix . '\\' : '') 
                        . $controller . '::' . $action . '()';
                })(),
                referer: $request->getHeaderLine('Referer') !== '' 
                    ? $request->getHeaderLine('Referer') : null,
                ip_address: $request->clientIp() !== '' 
                    ? $request->clientIp() : null,
                user_agent: $request->getHeaderLine('User-Agent') !== '' 
                    ? $request->getHeaderLine('User-Agent') : null,
                created: $accessed->format('Y-m-d\TH:i:s'),
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
