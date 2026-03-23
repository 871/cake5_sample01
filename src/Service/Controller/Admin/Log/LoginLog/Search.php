<?php
declare(strict_types=1);

namespace App\Service\Controller\Admin\Log\LoginLog;

use App\Model\Table\Log\LoginLogsTable;
use App\Security\Input\StrictCast;
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
        return [];
    }

    /**
     * @return \Cake\ORM\Query\SelectQuery<\App\Model\Entity\Log\LoginLog>
     */
    public function getSearchQuery(): SelectQuery
    {
        /** @var \App\Model\Table\Log\LoginLogsTable $table */
        $table = $this->fetchTable(LoginLogsTable::class);

        $query = $table->find()->orderByDesc('logged_in_at');

        /** @var array<string, string> $data */
        $data = $this->request->getQuery();

        if (!empty($data['login_id'])) {
            $query = $query->where(['LoginLogs.login_id' => StrictCast::toString($data['login_id'])]);
        }

        if (!empty($data['login_actor_type'])) {
            $query = $query->where(['LoginLogs.login_actor_type' => StrictCast::toString($data['login_actor_type'])]);
        }

        if (!empty($data['login_result'])) {
            $query = $query->where(['LoginLogs.login_result' => StrictCast::toString($data['login_result'])]);
        }

        if (!empty($data['ip_address'])) {
            $query = $query->where(['LoginLogs.ip_address LIKE' => '%' . StrictCast::toString($data['ip_address']) . '%']);
        }

        return $query;
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
                'id',
                'login_id',
                'login_actor_type',
                'login_result',
                'ip_address',
                'logged_in_at',
                'created',
            ],
            'order' => [
                'logged_in_at' => 'DESC',
            ],
        ];
    }
}
