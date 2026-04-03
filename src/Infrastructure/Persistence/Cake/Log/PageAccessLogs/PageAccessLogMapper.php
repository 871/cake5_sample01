<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Cake\Log\PageAccessLogs;

use App\Domain\Log\PageAccessLogs\Entity\PageAccessLog as DomainEntity;
use App\Model\Entity\Log\PageAccessLog as OrmEntity;
use App\Model\Table\Log\PageAccessLogsTable;
use App\Security\Input\Cast;
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
            id: Cast::toStringOrNull($ormEntity->id),
            accessed: Cast::toStringOrNull($ormEntity->accessed?->format('Y-m-d\TH:i:s.u')),
            account_type: Cast::toStringOrNull($ormEntity->account_type),
            account_id: Cast::toStringOrNull($ormEntity->account_id),
            method: Cast::toStringOrNull($ormEntity->method),
            path: Cast::toStringOrNull($ormEntity->path),
            query_string: Cast::toStringOrNull($ormEntity->query_string),
            post_keys: Cast::toStringOrNull($ormEntity->post_keys),
            route_name: Cast::toStringOrNull($ormEntity->route_name),
            referer: Cast::toStringOrNull($ormEntity->referer),
            ip_address: Cast::toStringOrNull($ormEntity->ip_address),
            user_agent: Cast::toStringOrNull($ormEntity->user_agent),
            created: Cast::toStringOrNull($ormEntity->created?->format('Y-m-d\TH:i:s')),
        );
    }
}
