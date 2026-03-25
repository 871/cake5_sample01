<?php
declare(strict_types=1);

namespace App\Service\Controller\Admin\Log\LoginLog;

use App\Domain\Log\LoginLogs\Entity\LoginLog as DomainEntity;
use App\Domain\Log\LoginLogs\ValueObject as Vo;
use App\Infrastructure\Persistence\Cake\Log\LoginLogs\LoginLogsRepository;
use App\Security\Input\StrictCast;
use App\Service\Controller\Shared\ServiceInterface;
use App\Service\Controller\Shared\ServiceTrait;

final class Detail implements ServiceInterface
{
    use ServiceTrait;

    /**
     * @return \App\Domain\Log\LoginLogs\Entity\LoginLog
     */
    public function getEntity(): DomainEntity
    {
        return (new LoginLogsRepository())->read(
            id: new Vo\Id(
                StrictCast::toString($this->request->getParam('login_log_id')),
            ),
        );
    }
}
