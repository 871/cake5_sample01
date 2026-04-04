<?php
declare(strict_types=1);

namespace App\Service\Controller\Admin\Log\PageAccessLog;

use App\Domain\Log\PageAccessLogs\AdminSearchCondition;
use App\Domain\Log\PageAccessLogs\ValueObject as Vo;
use App\Infrastructure\Persistence\Cake\Log\PageAccessLogs\PageAccessLogsRepository;
use App\Security\Input\Cast;
use App\Service\Controller\Shared\ServiceInterface;
use App\Service\Controller\Shared\ServiceTrait;
use Cake\ORM\Locator\LocatorAwareTrait;
use Cake\ORM\Query\SelectQuery;

final class Search implements ServiceInterface
{
    use ServiceTrait;
    use LocatorAwareTrait;

    /**
     * @return array<string, mixed>
     */
    public function getInitParams(): array
    {
        return [
            'accessed_from' => $this->datetime->format('Y-m-d\T00:00:00'),
            'accessed_to' => $this->datetime->format('Y-m-d\T23:59:59'),
        ];
    }

    /**
     * @return \Cake\ORM\Query\SelectQuery<\App\Model\Entity\Log\PageAccessLog>
     */
    public function getSearchQuery(): SelectQuery
    {
        /** @var array<string, string> $data */
        $data = $this->request->getQuery();

        $accountTypeValue = Cast::toStringOrNull($data['account_type'] ?? null);

        return (new PageAccessLogsRepository())->adminSearch(new AdminSearchCondition(
            accessedFrom: new Vo\Accessed(Cast::toDateTimeStringOrNull($data['accessed_from'] ?? null)),
            accessedTo: new Vo\Accessed(Cast::toDateTimeStringOrNull($data['accessed_to'] ?? null)),
            accountType: new Vo\AccountType(
                in_array($accountTypeValue, Vo\AccountType::VALUES, true) ? $accountTypeValue : null,
            ),
            accountId: new Vo\AccountId(Cast::toStringOrNull($data['account_id'] ?? null)),
            keyword: new Vo\Search\Keyword(Cast::toStringOrNull($data['keyword'] ?? null)),
        ));
    }

    /**
     * @return array<string, array<int|string, string>|int>
     */
    public function getPaginateSettings(): array
    {
        return [
            'limit' => 20,
            'maxLimit' => 200,
            'sortableFields' => [
                'accessed',
                'account_type',
                'account_id',
                'method',
                'path',
                'route_name',
                'ip_address',
                'created',
            ],
            'order' => [
                'accessed' => 'DESC',
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function getAccountTypeOptions(): array
    {
        return [
            Vo\AccountType::ADMIN => '管理者',
            Vo\AccountType::USER => 'ユーザー',
        ];
    }
}
