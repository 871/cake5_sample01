<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Cake\User\UserGrant;

use App\Domain\User\UserGrant\Entity\GrantPermission;
use App\Domain\User\UserGrant\Repository\UserGrantPermissionRepository as InterfaceRepo;
use App\Domain\User\UserGrant\SearchUserGrantPermissionCondition;
use App\Domain\User\UserGrant\ValueObject as Vo;
use App\Infrastructure\Persistence\Cake\User\UserGrant\UserGrantPermissionRepository\Create as CreateService;
use App\Infrastructure\Persistence\Cake\User\UserGrant\UserGrantPermissionRepository\Delete as DeleteService;
use App\Infrastructure\Persistence\Cake\User\UserGrant\UserGrantPermissionRepository\Detail as DetailService;
use App\Infrastructure\Persistence\Cake\User\UserGrant\UserGrantPermissionRepository\Search as SearchService;
use App\Infrastructure\Persistence\Cake\User\UserGrant\UserGrantPermissionRepository\Update as UpdateService;
use DateTimeInterface;

final class UserGrantPermissionRepository implements InterfaceRepo
{
    /**
     * @param \DateTimeInterface $datetime
     */
    public function __construct(private readonly DateTimeInterface $datetime)
    {
    }

    /**
     * @param \App\Domain\User\UserGrant\SearchUserGrantPermissionCondition $condition
     * @return array<\App\Domain\User\UserGrant\Entity\GrantPermission>
     */
    public function search(SearchUserGrantPermissionCondition $condition): array
    {
        return (new SearchService($condition))->run();
    }

    /**
     * @param \App\Domain\User\UserGrant\ValueObject\Code $code
     * @param \App\Domain\User\UserGrant\ValueObject\UserAccountId $accountId
     * @return bool
     */
    public function hasPermission(Vo\Code $code, Vo\UserAccountId $accountId): bool
    {
        return (new UserGrantPermissionRepository\HasPermission())->run($code, $accountId);
    }

    /**
     * @param \App\Domain\User\UserGrant\Entity\GrantPermission $entity
     * @return \App\Domain\User\UserGrant\Entity\GrantPermission
     */
    public function create(GrantPermission $entity): GrantPermission
    {
        return (new CreateService($this->datetime))->run($entity);
    }

    /**
     * @param \App\Domain\User\UserGrant\ValueObject\GrantPermissionId $id
     * @return \App\Domain\User\UserGrant\Entity\GrantPermission
     */
    public function read(Vo\GrantPermissionId $id): GrantPermission
    {
        return (new DetailService())->run($id);
    }

    /**
     * @param \App\Domain\User\UserGrant\Entity\GrantPermission $entity
     * @return \App\Domain\User\UserGrant\Entity\GrantPermission
     */
    public function update(GrantPermission $entity): GrantPermission
    {
        return (new UpdateService($this->datetime))->run($entity);
    }

    /**
     * @param \App\Domain\User\UserGrant\Entity\GrantPermission $entity
     * @return \App\Domain\User\UserGrant\Entity\GrantPermission
     */
    public function delete(GrantPermission $entity): GrantPermission
    {
        return (new DeleteService($this->datetime))->run($entity);
    }
}
