<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Cake\Admin\AdminGrant\AdminGrantRoleRepository;

use App\Domain\Admin\AdminGrant\Entity as De;
use App\Domain\Exception\RepositoryException;
use App\Domain\Shared\Enum as SEn;
use App\Model\Entity\Grant\GrantRole as OrmGrantRole;
use App\Model\Table\Grant\GrantRolesTable;
use Cake\ORM\Exception\PersistenceFailedException;
use Cake\ORM\Locator\LocatorAwareTrait;
use DateTimeInterface;
use DomainException;

final class Delete
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
                    $ormEntity = $this->table->find()
                        ->contain([
                            // Memo: Acount側の情報はここでは更新しない
                            'GrantRolePermissions',
                        ])->where([
                            'id' => $domainGrantRole->grantRoleId()->toString(),
                        ])
                        ->epilog('FOR UPDATE')
                        ->firstOrFail();

                    // 関連するアカウントロールが存在する場合は削除できない
                    if (
                        $this->table->GrantAccountRoles->exists([
                        'grant_role_id' => $domainGrantRole->grantRoleId()->toString(),
                        ])
                    ) {
                        throw new DomainException('Cannot delete grant role with associated account roles');
                    }

                    $this->table->GrantRolePermissions->deleteAll([
                        'grant_role_id' => $domainGrantRole->grantRoleId()->toString(),
                    ]);

                    $this->table->delete($ormEntity, [
                        'checkExisting' => false,
                    ]);

                    return $ormEntity;
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
