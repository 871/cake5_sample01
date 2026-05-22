<?php
declare(strict_types=1);

namespace App\Service\Controller\Admin\AdminGrant\Role;

use App\Domain\Admin\AdminGrant\Entity\GrantRole;
use App\Domain\Admin\AdminGrant\ValueObject as Vo;
use App\Domain\Shared\ValueObject\Created;
use App\Domain\Shared\ValueObject\Modified;
use App\Infrastructure\Persistence\Cake\Admin\AdminGrant\AdminGrantRoleRepository;
use App\Security\Input\Cast;
use App\Security\Input\StrictCast;
use App\Service\Controller\Shared\ServiceInterface;
use App\Service\Controller\Shared\ServiceTrait;

final class Update implements ServiceInterface
{
    use ServiceTrait;

    /**
     * @return \App\Domain\Admin\AdminGrant\Entity\GrantRole
     */
    public function getDomainEntity(): GrantRole
    {
        return (new AdminGrantRoleRepository($this->datetime))->read(new Vo\GrantRoleId(
            StrictCast::toString($this->request->getParam('grant_role_id')),
        ));
    }

    /**
     * @return \App\Domain\Admin\AdminGrant\Entity\GrantRole
     */
    public function update(): GrantRole
    {
        $current = $this->getDomainEntity();

        return (new AdminGrantRoleRepository($this->datetime))->update(new GrantRole(
            grant_role_id: $current->grantRoleId(),
            code: new Vo\Code(Cast::toStringOrNull($this->request->getData('code'))),
            name: new Vo\Name(Cast::toStringOrNull($this->request->getData('name'))),
            description: new Vo\Description(Cast::toStringOrNull($this->request->getData('description'))),
            sort: new Vo\Sort(Cast::toStringOrNull($this->request->getData('sort')) ?? '0'),
            is_active: new Vo\IsActive(Cast::toStringOrNull($this->request->getData('is_active')) ?? '1'),
            created: new Created($current->created()->format('Y-m-d\TH:i:s')),
            modified: new Modified($this->datetime->format('Y-m-d\TH:i:s')),
            grant_account_roles: [],
            grant_role_permissions: $current->grantRolePermissions(),
        ));
    }
}
