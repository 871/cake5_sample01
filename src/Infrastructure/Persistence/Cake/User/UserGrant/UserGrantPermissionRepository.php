<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Cake\User\UserGrant;

use App\Domain\User\UserGrant\Entity\GrantPermission;
use App\Domain\User\UserGrant\Repository\UserGrantPermissionRepository as InterfaceRepo;
use App\Domain\User\UserGrant\ValueObject as Vo;
use App\Infrastructure\Persistence\Cake\User\UserGrant\UserGrantPermissionRepository\Search as SearchService;
use App\Infrastructure\Persistence\Cake\User\UserGrant\UserGrantPermissionRepository\Create as CreateService;
use App\Infrastructure\Persistence\Cake\User\UserGrant\UserGrantPermissionRepository\Detail as DetailService;
use App\Infrastructure\Persistence\Cake\User\UserGrant\UserGrantPermissionRepository\Update as UpdateService;
use App\Infrastructure\Persistence\Cake\User\UserGrant\UserGrantPermissionRepository\Delete as DeleteService;
use DateTimeInterface;

final class UserGrantPermissionRepository implements InterfaceRepo
{
    public function __construct(private readonly DateTimeInterface $datetime)
    {
    }

    public function search(mixed $condition): array
    {
        return (new SearchService($condition))->run();
    }

    public function create(GrantPermission $entity): GrantPermission
    {
        return (new CreateService($this->datetime))->run($entity);
    }

    public function read(Vo\GrantPermissionId $id): GrantPermission
    {
        return (new DetailService())->run($id);
    }

    public function update(GrantPermission $entity): GrantPermission
    {
        return (new UpdateService($this->datetime))->run($entity);
    }

    public function delete(GrantPermission $entity): GrantPermission
    {
        return (new DeleteService($this->datetime))->run($entity);
    }
}
