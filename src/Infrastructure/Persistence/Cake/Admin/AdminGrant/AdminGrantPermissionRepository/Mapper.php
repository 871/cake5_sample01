<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Cake\Admin\AdminGrant\AdminGrantPermissionRepository;

use App\Model\Entity\Grant\GrantPermission as OrmEntityGrantPermission;
use App\Domain\Admin\AdminGrant\Entity\GrantPermission  as DomainEntityGrantPermission;
use App\Domain\Admin\AdminGrant\ValueObject as Vo;
use App\Domain\Shared\ValueObject as SVo;

final class Mapper
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        // 処理なし
    }

    public static function toDomainGrantPermission(
        OrmEntityGrantPermission $ormGrantPermission
    ): DomainEntityGrantPermission {
        return new DomainEntityGrantPermission(
            grant_permission_id: new Vo\GrantPermissionId((string)$ormGrantPermission->id),
            code: new Vo\Code((string)$ormGrantPermission->code),
            name: new Vo\Name((string)$ormGrantPermission->name),
            description: new Vo\Description((string)$ormGrantPermission->description),
            sort: new Vo\Sort((string)$ormGrantPermission->sort),
            is_active: new Vo\IsActive((string)$ormGrantPermission->is_active),
            created: new SVo\Created($ormGrantPermission->created->format('Y-m-d\TH:i:s')),
            modified: new SVo\Modified($ormGrantPermission->modified->format('Y-m-d\TH:i:s')),
        );
    }
}
