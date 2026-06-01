<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Cake\User\UserGrant\UserGrantPermissionRepository;

use App\Domain\User\UserGrant\Entity as De;
use App\Domain\Exception\RepositoryException;
use App\Domain\Shared\Enum as SEn;
use App\Model\Entity\Grant\GrantPermission as OrmGrantPermission;
use App\Model\Table\Grant\GrantPermissionsTable;
use Cake\ORM\Exception\PersistenceFailedException;
use Cake\ORM\Locator\LocatorAwareTrait;
use DateTimeInterface;
use DomainException;

final class Delete
{
    use LocatorAwareTrait;

    public const ACCOUNT_TYPE = SEn\AccountType::USER->value;

    private GrantPermissionsTable $table;

    public function __construct(private readonly DateTimeInterface $datetime)
    {
        $this->table = $this->fetchTable(GrantPermissionsTable::class);
    }

    public function run(De\GrantPermission $entity): De\GrantPermission
    {
        // 日時は未使用だが、クラス設計上コンストラクタで注入されるため静的解析のために参照する
        // 参照のみ行い、静的解析で未使用と判定されないようにする
        $unused = $this->datetime;
        unset($unused);
        try {
            $orm = $this->table->getConnection()->transactional(function () use ($entity): OrmGrantPermission {
                $saved = $this->table->find()->where([
                    'id' => $entity->grantPermissionId()->toString(),
                    'GrantPermissions.account_type' => self::ACCOUNT_TYPE,
                ])->epilog('FOR UPDATE')->firstOrFail();

                // 関連するロール権限が存在する場合は削除不可
                if ($this->table->GrantRolePermissions->exists(['grant_permission_id' => $entity->grantPermissionId()->toString()])) {
                    throw new DomainException('Cannot delete grant permission with associated role permissions');
                }

                $this->table->delete($saved, ['checkExisting' => false]);

                return $saved;
            });

            return Mapper::toDomainGrantPermission($orm);
        } catch (PersistenceFailedException $ex) {
            throw new RepositoryException('UserGrantPermissionRepository Delete Error', previous: $ex);
        }
    }
}
