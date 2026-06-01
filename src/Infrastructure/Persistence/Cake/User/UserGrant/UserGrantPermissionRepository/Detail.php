<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Cake\User\UserGrant\UserGrantPermissionRepository;

use App\Domain\User\UserGrant\Entity\GrantPermission as DomainEntity;
use App\Domain\User\UserGrant\ValueObject as Vo;
use App\Model\Table\Grant\GrantPermissionsTable;
use Cake\ORM\Locator\LocatorAwareTrait;

final class Detail
{
    use LocatorAwareTrait;

    private GrantPermissionsTable $table;

    public function __construct()
    {
        $this->table = $this->fetchTable(GrantPermissionsTable::class);
    }

    public function run(Vo\GrantPermissionId $id): DomainEntity
    {
        $orm = $this->table->find()->where(['id' => $id->toString(), 'GrantPermissions.account_type' => Mapper::ACCOUNT_TYPE])->firstOrFail();
        return Mapper::toDomainGrantPermission($orm);
    }
}
