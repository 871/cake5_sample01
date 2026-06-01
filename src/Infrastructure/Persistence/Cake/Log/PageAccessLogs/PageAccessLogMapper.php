<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Cake\Log\PageAccessLogs;

use App\Domain\Log\PageAccessLogs\Entity\PageAccessLog as DomainEntity;
use App\Domain\Log\PageAccessLogs\ValueObject as Vo;
use App\Domain\Shared\ValueObject as SVo;
use App\Model\Entity\Log\PageAccessLog as OrmEntity;
use App\Model\Table\Log\PageAccessLogsTable;
use App\Security\Input\Cast;
use App\Security\Input\StrictCast;
use Cake\ORM\Locator\LocatorAwareTrait;

final class PageAccessLogMapper
{
    use LocatorAwareTrait;

    /**
     * @var \App\Model\Table\Log\PageAccessLogsTable
     */
    private PageAccessLogsTable $table;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->table = $this->fetchTable(PageAccessLogsTable::class);
    }

    /**
     * @param \App\Domain\Log\PageAccessLogs\Entity\PageAccessLog $domainEntity
     * @return \App\Model\Entity\Log\PageAccessLog
     */
    public function toNewOrmEntity(DomainEntity $domainEntity): OrmEntity
    {
        $entity = $this->table->newEntity([
            'id' => $domainEntity->id()->toString(),
            'accessed' => $domainEntity->accessed()->format('Y-m-d\TH:i:s.u'),
            'account_type' => $domainEntity->accountType()->toString(),
            'account_id' => $domainEntity->accountId()->toInt(),
            'method' => $domainEntity->method()->toString(),
            'path' => $domainEntity->path()->toString(),
            'query_string' => $domainEntity->queryString()->toStringOrNull(),
            'post_keys' => $domainEntity->postKeys()->toStringOrNull(),
            'route_name' => $domainEntity->routeName()->toStringOrNull(),
            'referer' => $domainEntity->referer()->toStringOrNull(),
            'ip_address' => $domainEntity->ipAddress()->toStringOrNull(),
            'user_agent' => $domainEntity->userAgent()->toStringOrNull(),
            'created' => $domainEntity->created()->toDateTimeOrNull()?->format('Y-m-d\TH:i:s'),
        ], [
            'validate' => false,
        ]);

        return $entity;
    }

    /**
     * @param \App\Model\Entity\Log\PageAccessLog $ormEntity
     * @return \App\Domain\Log\PageAccessLogs\Entity\PageAccessLog
     */
    public function toDomainEntity(OrmEntity $ormEntity): DomainEntity
    {
        return new DomainEntity(
            id: Vo\Id::fromString(StrictCast::toString($ormEntity->id)),
            accessed: new Vo\Accessed(StrictCast::toString($ormEntity->accessed->format('Y-m-d\TH:i:s.u'))),
            account_type: Vo\AccountType::fromString(StrictCast::toString($ormEntity->account_type)),
            account_id: new Vo\AccountId(StrictCast::toString($ormEntity->account_id)),
            method: Vo\Method::fromString(StrictCast::toString($ormEntity->method)),
            path: Vo\Path::fromString(StrictCast::toString($ormEntity->path)),
            query_string: Vo\QueryString::fromString(Cast::toStringOrNull($ormEntity->query_string)),
            post_keys: Vo\PostKeys::fromString(Cast::toStringOrNull($ormEntity->post_keys)),
            route_name: Vo\RouteName::fromString(Cast::toStringOrNull($ormEntity->route_name)),
            referer: Vo\Referer::fromString(Cast::toStringOrNull($ormEntity->referer)),
            ip_address: Vo\IpAddress::fromString(Cast::toStringOrNull($ormEntity->ip_address)),
            user_agent: Vo\UserAgent::fromString(Cast::toStringOrNull($ormEntity->user_agent)),
            created: new SVo\Created(StrictCast::toString($ormEntity->created->format('Y-m-d\TH:i:s'))),
            search_key: Vo\SearchKey::fromString(Cast::toStringOrNull($ormEntity->search_key)),
        );
    }
}
