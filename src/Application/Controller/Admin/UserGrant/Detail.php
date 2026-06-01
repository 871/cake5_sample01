<?php
declare(strict_types=1);

namespace App\Application\Controller\Admin\UserGrant;

use App\Application\Controller\Shared\ServiceInterface;
use App\Application\Controller\Shared\ServiceTrait;
use App\Domain\User\UserGrant\Entity\UserAccountGrant;
use App\Domain\User\UserGrant\SearchUserGrantPermissionCondition;
use App\Domain\User\UserGrant\ValueObject as Vo;
use App\Domain\Shared\ValueObject as SVo;
use App\Infrastructure\Persistence\Cake\User\UserGrant\UserAccountGrantRepository;
use App\Infrastructure\Persistence\Cake\User\UserGrant\UserGrantPermissionRepository;

final class Detail implements ServiceInterface
{
    use ServiceTrait;

    /**
     * @param string $userAccountId
     * @return \App\Domain\User\UserGrant\Entity\UserAccountGrant
     */
    public function getUserAccountGrant(string $userAccountId): UserAccountGrant
    {
        return (new UserAccountGrantRepository($this->datetime))->detail(new Vo\UserAccountId($userAccountId));
    }

    /**
     * @return array<\App\Domain\User\UserGrant\Entity\GrantPermission>
     */
    public function getGrantPermissions(): array
    {
        return (new UserGrantPermissionRepository($this->datetime))->search(new SearchUserGrantPermissionCondition(
            searchText: new SVo\SearchText(''),
            isActive: new Vo\IsActive('1'),
        ));
    }
}
