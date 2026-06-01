<?php
declare(strict_types=1);

namespace App\Application\Controller\Admin\UserGrant\Role;

use App\Application\Controller\Shared\ServiceInterface;
use App\Application\Controller\Shared\ServiceTrait;
use App\Domain\User\UserGrant\Entity\GrantRole;
use App\Domain\User\UserGrant\ValueObject\GrantRoleId;
use App\Infrastructure\Persistence\Cake\User\UserGrant\UserGrantRoleRepository;
use App\Security\Input\StrictCast;

final class Delete implements ServiceInterface
{
    use ServiceTrait;

    /**
     * @return \App\Domain\User\UserGrant\Entity\GrantRole
     */
    public function delete(): GrantRole
    {
        $repository = new UserGrantRoleRepository($this->datetime);

        return $repository->delete($repository->read(new GrantRoleId(
            StrictCast::toString($this->request->getParam('grant_role_id')),
        )));
    }
}
