<?php
declare(strict_types=1);

namespace App\Domain\User\UserGrant\Repository;

use App\Domain\User\UserGrant\Entity\GrantPermission;
use App\Domain\User\UserGrant\ValueObject as Vo;
use DateTimeInterface;

interface UserGrantPermissionRepository
{
    /**
     * @param \DateTimeInterface $datetime
     */
    public function __construct(DateTimeInterface $datetime);

    /**
     * @param mixed $condition
     * @return array<GrantPermission>
     */
    public function search(mixed $condition): array;

    /**
     * @param GrantPermission $entity
     * @return GrantPermission
     */
    public function create(GrantPermission $entity): GrantPermission;

    /**
     * @param Vo\GrantPermissionId $id
     * @return GrantPermission
     */
    public function read(Vo\GrantPermissionId $id): GrantPermission;

    /**
     * @param GrantPermission $entity
     * @return GrantPermission
     */
    public function update(GrantPermission $entity): GrantPermission;

    /**
     * @param GrantPermission $entity
     * @return GrantPermission
     */
    public function delete(GrantPermission $entity): GrantPermission;
}
