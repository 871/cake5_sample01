<?php
declare(strict_types=1);

namespace App\Service\Controller\Admin\Log\LoginLog;

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
        // TODO 未実装
        return [];
    }

    /**
     * @return \Cake\ORM\Query\SelectQuery<\App\Model\Entity\Log\LoginLog>
     */
    public function getSearchQuery(): SelectQuery
    {
        // TODO 未実装
        throw new \RuntimeException('未実装');
    }

    /**
     * @return array<string, array<int|string, string>|int>
     */
    public function getPaginateSettings(): array
    {
        // TODO 未実装
        return [];
    }
}
