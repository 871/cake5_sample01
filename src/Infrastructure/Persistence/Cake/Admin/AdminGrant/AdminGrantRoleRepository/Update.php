<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Cake\Admin\AdminGrant\AdminGrantRoleRepository;

use App\Domain\Admin\AdminGrant\Entity as De;
use App\Domain\Exception\RepositoryException;
use App\Domain\Shared\Enum as SEn;
use App\Model\Entity\Grant\GrantRole as OrmGrantRole;
use App\Model\Table\Grant\GrantRolesTable;
use App\Lib\UUID\UUID;
use Cake\ORM\Exception\PersistenceFailedException;
use Cake\ORM\Locator\LocatorAwareTrait;
use DateTimeInterface;

final class Update
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
    public function run(De\GrantRole $domainGrantRole): De\GrantRole
    {
        try {
            /** @var \App\Model\Entity\Grant\GrantRole $ormEntity */
            $ormEntity = $this->table->getConnection()->transactional(
                function () use ($domainGrantRole): OrmGrantRole {
                    // テーブルロック
                    $savedEntity = $this->table->find()
                        ->contain([
                            // Memo: Acount側の情報はここでは更新しない
                            'GrantRolePermissions',
                        ])->where([
                            'id' => $domainGrantRole->grantRoleId()->toString(),
                            'modified' => $domainGrantRole->modified()->format('Y-m-d\TH:i:s'),
                        ])
                        ->epilog('FOR UPDATE')
                        ->firstOrFail();

                    $this->table->patchEntity(
                        $savedEntity,
                        [
                            'code' => $domainGrantRole->code()->toString(),
                            'name' => $domainGrantRole->name()->toString(),
                            'description' => $domainGrantRole->description()->toStringOrNull(),
                            'sort' => $domainGrantRole->sort()->toInt(),
                            'is_active' => $domainGrantRole->isActive()->toInt(),
                            'modified' => $this->datetime->format('Y-m-d\TH:i:s'),
                            'grant_role_permissions' => array_map(
                                function (
                                    De\GrantRolePermission $domainGrantRolePermission,
                                ) {
                                    return [
                                        'id' => UUID::uuid7(),
                                        'account_type' => self::ACCOUNT_TYPE,
                                        'grant_role_id' => $domainGrantRolePermission->grantRoleId()->toString(),
                                        'grant_permission_id' => $domainGrantRolePermission
                                            ->grantPermissionId()
                                            ->toString(),
                                        'created' => $this->datetime->format('Y-m-d\TH:i:s'),
                                        'modified' => $this->datetime->format('Y-m-d\TH:i:s'),
                                    ];
                                },
                                $domainGrantRole->grantRolePermissions(),
                            ),
                        ],
                    );

                    $this->table->GrantRolePermissions->deleteAll([
                        'grant_role_id' => $domainGrantRole->grantRoleId()->toString(),
                    ]);

                    $this->table->saveOrFail($savedEntity, [
                        'checkExisting' => false,
                        'associated' => [
                            'GrantRolePermissions',
                        ],
                    ]);

                    return $savedEntity;
                },
            );

            return $this->mapper->toDomainGrantRole($ormEntity);
        } catch (PersistenceFailedException $ex) {
            throw new RepositoryException(
                message: 'AdminGrantRoleRepository Update Error',
                previous: $ex,
            );
        }
    }
}
