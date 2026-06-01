<?php
declare(strict_types=1);

namespace App\Middleware\User;

use App\Domain\Log\PageAccessLogs\Entity\PageAccessLog;
use App\Domain\Log\PageAccessLogs\ValueObject as Vo;
use App\Domain\Shared\ValueObject as SVo;
use App\Infrastructure\Persistence\Cake\Log\PageAccessLogs\PageAccessLogsRepository;
use App\Lib\UUID\UUID;
use App\Security\Auth\AuthContextResolver;
use App\Security\Input\StrictCast;
use Cake\Log\Log;
use DateTimeImmutable;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Throwable;

class PageAccessLogMiddleware implements MiddlewareInterface
{
    /**
     * @var int
     */
    private int $errorCoutn = 0;

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

            // 認証コンテキストからユーザーのアカウントIDを解決する
            $authContext = AuthContextResolver::resolve($request);
            $accountIdStr = StrictCast::toString($authContext->getAccountId()->toString());

            // 未認証(匿名)や不正なIDの場合はログ作成をスキップする
            if ($accountIdStr === '' || $accountIdStr === '0') {
                return $response;
            }

            $entity = new PageAccessLog(
                id: Vo\Id::fromString(UUID::uuid7()),
                accessed: new Vo\Accessed($accessed->format('Y-m-d\TH:i:s.u')),
                account_type: Vo\AccountType::fromString(Vo\AccountType::USER),
                account_id: new Vo\AccountId(StrictCast::toString($accountIdStr)),
                method: Vo\Method::fromString($request->getMethod()),
                path: Vo\Path::fromString($uri->getPath()),
                query_string: Vo\QueryString::fromString($uri->getQuery() !== '' ? $uri->getQuery() : null),
                post_keys: Vo\PostKeys::fromString((function () use ($request) {
                    $parsedBody = $request->getParsedBody();

                    $result = is_array($parsedBody) && $parsedBody !== []
                        ? json_encode(array_keys($parsedBody))
                        : null;

                    return $result === false ? null : $result;
                })()),
                route_name: Vo\RouteName::fromString((function () use ($request) {
                    $prefix = StrictCast::toString($request->getParam('prefix'));
                    $controller = StrictCast::toString($request->getParam('controller'));
                    $action = StrictCast::toString($request->getParam('action'));

                    if ($controller === '' || $action === '') {
                        return null;
                    }

                    return 'App\\Controller\\'
                        . ($prefix !== '' ? preg_replace('/\//', '\\\\', $prefix) . '\\' : '')
                        . $controller . '::' . $action . '()';
                })()),
                referer: Vo\Referer::fromString(
                    $request->getHeaderLine('Referer') !== '' ? $request->getHeaderLine('Referer') : null,
                ),
                ip_address: Vo\IpAddress::fromString(
                    $request->clientIp() !== '' ? $request->clientIp() : null,
                ),
                user_agent: Vo\UserAgent::fromString(
                    $request->getHeaderLine('User-Agent') !== '' ? $request->getHeaderLine('User-Agent') : null,
                ),
                created: new SVo\Created($accessed->format('Y-m-d\TH:i:s')),
                search_key: Vo\SearchKey::fromString(null),
            );

            $this->repository->create($entity);
        } catch (Throwable $e) {
            $this->errorCoutn++;
            if ($this->errorCoutn < 5) {
                return $this->process($request, $handler);
            }

            // ログ記録の失敗はリクエストの処理に影響させない
            Log::error(sprintf(
                'PageAccessLogMiddleware(User): failed to create log. %s: %s',
                get_class($e),
                $e->getMessage(),
            ));
        }

        return $response;
    }
}
