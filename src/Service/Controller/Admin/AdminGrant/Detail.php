<?php
declare(strict_types=1);

namespace App\Service\Controller\Admin\AdminGrant;

use App\Domain\Admin\AdminGrant\ValueObject\AdminAccountId;
use App\Infrastructure\Persistence\Cake\Admin\AdminGrantRepository;
use App\Service\Controller\Shared\ServiceInterface;
use App\Service\Controller\Shared\ServiceTrait;

final class Detail implements ServiceInterface
{
    use ServiceTrait;

    /**
     * @param string $adminAccountId
     * @return array<\App\Domain\Admin\AdminGrant\Entity\GrantPermission>
     */
    public function getAccountPermissions(string $adminAccountId): array
    {
        return (new AdminGrantRepository($this->datetime))->getAccountPermissions(new AdminAccountId($adminAccountId));
    }
}
