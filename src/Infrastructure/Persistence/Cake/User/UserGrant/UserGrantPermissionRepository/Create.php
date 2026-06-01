<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Cake\User\UserGrant\UserGrantPermissionRepository;

use App\Domain\User\UserGrant\Entity as De;
use App\Domain\User\UserGrant\ValueObject as Vo;
use App\Domain\Shared\Enum as SEn;
use App\Lib\UUID\UUID;
use App\Model\Entity\Grant\GrantPermission as OrmGrantPermission;
use App\Model\Table\Grant\GrantPermissionsTable;
use Cake\ORM\Locator\LocatorAwareTrait;
use DateTimeInterface;

final class Create
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
        $saved = $this->table->getConnection()->transactional(function () use ($entity): OrmGrantPermission {
            $new = $this->table->newEntity([
                'account_type' => self::ACCOUNT_TYPE,
                'code' => $entity->code()->toString(),
                'name' => $entity->name()->toString(),
                'description' => $entity->description()->toStringOrNull(),
                'sort' => $entity->sort()->toInt(),
                'is_active' => $entity->isActive()->toInt(),
                'created' => $this->datetime->format('Y-m-d\TH:i:s'),
                'modified' => $this->datetime->format('Y-m-d\TH:i:s'),
            ]);

            $this->table->saveOrFail($new, ['checkExisting' => false]);

            return $new;
        });

        return (new Detail())->run(new Vo\GrantPermissionId((string)$saved->id));
    }
}
