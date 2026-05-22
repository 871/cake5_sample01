<?php
declare(strict_types=1);

namespace App\Service\Controller\Admin\AdminGrant\Role;

use App\Domain\Admin\AdminGrant\Entity\GrantRole;
use App\Domain\Admin\AdminGrant\ValueObject\GrantRoleId;
use App\Infrastructure\Persistence\Cake\Admin\AdminGrant\AdminGrantRoleRepository;
use App\Security\Input\StrictCast;
use App\Service\Controller\Shared\ServiceInterface;
use App\Service\Controller\Shared\ServiceTrait;

final class Delete implements ServiceInterface
{
    use ServiceTrait;

    /**
     * @return \App\Domain\Admin\AdminGrant\Entity\GrantRole
     */
    public function delete(): GrantRole
    {
        $repository = new AdminGrantRoleRepository($this->datetime);

        return $repository->delete($repository->read(new GrantRoleId(
            StrictCast::toString($this->request->getParam('grant_role_id')),
        )));
    }
}
