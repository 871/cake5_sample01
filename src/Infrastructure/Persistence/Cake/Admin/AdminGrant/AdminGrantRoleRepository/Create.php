<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Cake\Admin\AdminGrant\AdminGrantRoleRepository;

use App\Domain\Admin\AdminGrant\Entity as De;
use App\Domain\Admin\AdminGrant\ValueObject as Vo;
use App\Domain\Shared\Enum as SEn;
use App\Model\Entity\Grant\GrantRole as OrmGrantRole;
use App\Model\Table\Grant\GrantRolesTable;
use Cake\ORM\Locator\LocatorAwareTrait;
use DateTimeInterface;

final class Create
{
    use LocatorAwareTrait;

    public const ACCOUNT_TYPE = SEn\AccountType::ADMIN->value;

    /**
     * @var \App\Model\Table\Grant\GrantRolesTable
     */
    private GrantRolesTable $table;

    /**
     * @var \App\Infrastructure\Persistence\Cake\Admin\AdminGrant\AdminGrantRoleRepository\Mapper
     */
    private Mapper $mapper;

    /**
     * @param \DateTimeInterface $datetime
     */
    public function __construct(
        private readonly DateTimeInterface $datetime,
    ) {
        $this->table = $this->fetchTable(GrantRolesTable::class);
        $this->mapper = new Mapper($this->datetime);
    }

    /**
     * @param \App\Domain\Admin\AdminGrant\Entity\GrantRole $domainEntity
     * @return \App\Domain\Admin\AdminGrant\Entity\GrantRole
     */
    public function run(De\GrantRole $domainEntity): De\GrantRole
    {
        $newEntity = $this->mapper->toNewOrmGrantRole($domainEntity);
        /** @var \App\Model\Entity\Grant\GrantRole $savedEntity */
        $savedEntity = $this->table->getConnection()->transactional(function () use ($domainEntity, $newEntity): OrmGrantRole {
            $this->table->saveOrFail($newEntity, [
                'checkExisting' => false,
            ]);

            if ($domainEntity->grantRolePermissions() !== []) {
                $grantRolePermissions = $this->table->GrantRolePermissions->newEntities(array_map(
                    fn(De\GrantRolePermission $grantRolePermission) => [
                        'id' => $grantRolePermission->grantRolePermissionId()->toString(),
                        'account_type' => self::ACCOUNT_TYPE,
                        'grant_role_id' => (string)$newEntity->id,
                        'grant_permission_id' => $grantRolePermission->grantPermissionId()->toString(),
                        'created' => $grantRolePermission->created()->format('Y-m-d\TH:i:s'),
                        'modified' => $grantRolePermission->modified()->format('Y-m-d\TH:i:s'),
                    ],
                    $domainEntity->grantRolePermissions(),
                ));

                $this->table->GrantRolePermissions->saveManyOrFail($grantRolePermissions, [
                    'checkExisting' => false,
                ]);
            }

            return $newEntity;
        });

        return (new Detail($this->datetime))->run(new Vo\GrantRoleId((string)$savedEntity->id));
    }
}
