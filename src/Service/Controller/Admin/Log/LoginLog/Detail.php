<?php
declare(strict_types=1);

namespace App\Service\Controller\Admin\Log\LoginLog;

use App\Model\Entity\Log\LoginLog;
use App\Model\Table\Log\LoginLogsTable;
use App\Security\Input\StrictCast;
use App\Service\Controller\Shared\ServiceInterface;
use App\Service\Controller\Shared\ServiceTrait;
use Cake\Http\Exception\NotFoundException;
use Cake\ORM\Locator\LocatorAwareTrait;

final class Detail implements ServiceInterface
{
    use ServiceTrait;
    use LocatorAwareTrait;

    /**
     * @return \App\Model\Entity\Log\LoginLog
     */
    public function getEntity(): LoginLog
    {
        /** @var \App\Model\Table\Log\LoginLogsTable $table */
        $table = $this->fetchTable(LoginLogsTable::class);

        $id = StrictCast::toString($this->request->getParam('login_log_id'));

        /** @var \App\Model\Entity\Log\LoginLog|null $entity */
        $entity = $table->find()->where(['LoginLogs.id' => $id])->first();

        if ($entity === null) {
            throw new NotFoundException('ログインログが見つかりません。[id: ' . $id . ']');
        }

        return $entity;
    }
}
