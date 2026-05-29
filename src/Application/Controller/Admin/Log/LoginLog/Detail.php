<?php
declare(strict_types=1);

namespace App\Application\Controller\Admin\Log\LoginLog;

use App\Application\Controller\Shared\ServiceInterface;
use App\Application\Controller\Shared\ServiceTrait;
use App\Domain\Log\LoginLogs\Entity\LoginLog as DomainEntity;
use App\Domain\Log\LoginLogs\ValueObject as Vo;
use App\Infrastructure\Persistence\Cake\Log\LoginLogs\LoginLogsRepository;
use App\Security\Input\StrictCast;

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
