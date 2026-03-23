<?php
declare(strict_types=1);

namespace App\Service\Controller\Admin\Log\LoginLog;

use App\Model\Entity\Log\LoginLog;
use App\Service\Controller\Shared\ServiceInterface;
use App\Service\Controller\Shared\ServiceTrait;

final class Detail implements ServiceInterface
{
    use ServiceTrait;

    /**
     * @return \App\Model\Entity\Log\LoginLog
     */
    public function getEntity(): LoginLog
    {
        // TODO 未実装
        throw new \RuntimeException('未実装');
    }
}
