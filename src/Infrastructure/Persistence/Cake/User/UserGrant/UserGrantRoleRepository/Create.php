<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Cake\User\UserGrant\UserGrantRoleRepository;

use App\Domain\User\UserGrant\Entity as De;
use App\Domain\User\UserGrant\ValueObject as Vo;
use App\Domain\Shared\Enum as SEn;
use App\Lib\UUID\UUID;
use App\Model\Entity\Grant\GrantRole as OrmGrantRole;
use App\Model\Table\Grant\GrantRolesTable;
use Cake\ORM\Locator\LocatorAwareTrait;
use DateTimeInterface;

final class Create
{
    use LocatorAwareTrait;

    public const ACCOUNT_TYPE = SEn\AccountType::USER->value;

    /**
     * @var \App\Model\Table\Grant\GrantRolesTable
     */
    private GrantRolesTable $table;

    /**
     * @param \DateTimeInterface $datetime
     */
    public function __construct(
        private readonly DateTimeInterface $datetime,
    ) {
        $this->table = $this->fetchTable(GrantRolesTable::class);
    }

    /**
     * @param \App\Domain\User\UserGrant\Entity\GrantRole $domainEntity
     * @return \App\Domain\User\UserGrant\Entity\GrantRole
     */
    public function run(De\GrantRole $domainEntity): De\GrantRole
    {
        /** @var \App\Model\Entity\Grant\GrantRole $savedEntity */
        $savedEntity = $this->table->getConnection()->transactional(function () use ($domainEntity): OrmGrantRole {
            $newEntity = $this->table->newEntity([
                'account_type' => self::ACCOUNT_TYPE,
                'code' => $domainEntity->code()->toString(),
                'name' => $domainEntity->name()->toString(),
                'description' => $domainEntity->description()->toString(),
                'sort' => $domainEntity->sort()->toInt(),
                'is_active' => $domainEntity->isActive()->toInt(),
                'created' => $this->datetime->format('Y-m-d\TH:i:s'),
                'modified' => $this->datetime->format('Y-m-d\TH:i:s'),
            ]);
            $this->table->saveOrFail($newEntity, [
                'checkExisting' => false,
            ]);

            /** @var array<int, array<string, mixed>> $grantRolePermissionsData */
            $grantRolePermissionsData = array_map(
                fn(De\GrantRolePermission $grantRolePermission) => [
                    'id' => UUID::uuid7(),
                    'account_type' => self::ACCOUNT_TYPE,
                    'grant_role_id' => (string)$newEntity->id,
                    'grant_permission_id' => $grantRolePermission->grantPermissionId()->toString(),
                    'created' => $this->datetime->format('Y-m-d\TH:i:s'),
                    'modified' => $this->datetime->format('Y-m-d\TH:i:s'),
                ],
                $domainEntity->grantRolePermissions(),
            );
            $grantRolePermissions = $this->table->GrantRolePermissions->newEntities($grantRolePermissionsData);
            $this->table->GrantRolePermissions->saveManyOrFail($grantRolePermissions, [
                'checkExisting' => false,
            ]);

            return $newEntity;
        });

        return (new Detail($this->datetime))->run(new Vo\GrantRoleId((string)$savedEntity->id));
    }
}
