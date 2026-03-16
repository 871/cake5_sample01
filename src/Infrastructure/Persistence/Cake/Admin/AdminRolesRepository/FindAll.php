<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Cake\Admin\AdminRolesRepository;

use App\Domain\Admin\AdminRoles\Entity\AdminRole as DomainEntity;
use App\Model\Entity\Admin\AdminRole as OrmEntity;
use App\Model\Table\Admin\AdminRolesTable;
use App\Security\Input\Cast;
use Cake\ORM\Locator\LocatorAwareTrait;

final class FindAll
{
    use LocatorAwareTrait;

    /**
     * @var \App\Model\Table\Admin\AdminRolesTable
     */
    private AdminRolesTable $table;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->table = $this->fetchTable(AdminRolesTable::class);
    }

    /**
     * @return \App\Domain\Admin\AdminRoles\Entity\AdminRole[]
     */
    public function run(): array
    {
        return $this->table
            ->find()
            ->select([
                'AdminRoles__id' => 'AdminRoles.id',
                'AdminRoles__name' => 'AdminRoles.name',
            ])
            ->order(['AdminRoles.name' => 'ASC'])
            ->formatResults(function ($results) {
                return $results->map(function (OrmEntity $entity) {
                    return new DomainEntity(
                        id: Cast::toString($entity->id),
                        name: Cast::toString($entity->name),
                    );
                });
            })
            ->toArray();
    }
}
