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

final class Update
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
        try {
            $orm = $this->table->getConnection()->transactional(function () use ($entity): OrmGrantPermission {
                $saved = $this->table->find()->where([
                    'id' => $entity->grantPermissionId()->toString(),
                    'modified' => $entity->modified()->format('Y-m-d\\TH:i:s'),
                    'GrantPermissions.account_type' => self::ACCOUNT_TYPE,
                ])->epilog('FOR UPDATE')->firstOrFail();

                /** @var OrmGrantPermission $saved */
                $table = $this->table;
                $table->patchEntity($saved, [
                    'code' => $entity->code()->toString(),
                    'name' => $entity->name()->toString(),
                    'description' => $entity->description()->toStringOrNull(),
                    'sort' => $entity->sort()->toInt(),
                    'is_active' => $entity->isActive()->toInt(),
                    'modified' => $this->datetime->format('Y-m-d\TH:i:s'),
                ]);

                $table->saveOrFail($saved, ['checkExisting' => false]);

                return $saved;
            });

            return Mapper::toDomainGrantPermission($orm);
        } catch (PersistenceFailedException $ex) {
            throw new RepositoryException('UserGrantPermissionRepository Update Error', previous: $ex);
        }
    }
}
