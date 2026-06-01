<?php
declare(strict_types=1);

namespace App\Domain\User\UserGrant\Repository;

use App\Domain\User\UserGrant\Entity\GrantRole;
use App\Domain\User\UserGrant\ValueObject as Vo;
use Cake\ORM\Query\SelectQuery;
use DateTimeInterface;

interface UserGrantRoleRepository
{
    /**
     * @param \DateTimeInterface $datetime
     */
    public function __construct(DateTimeInterface $datetime);

    /**
     * @param mixed $condition
     * @return SelectQuery<\App\Model\Entity\Grant\GrantRole>
     */
    public function query(mixed $condition): SelectQuery;

    /**
     * @param mixed $condition
     * @return array<GrantRole>
     */
    public function search(mixed $condition): array;

    /**
     * @param GrantRole $entity
     * @return GrantRole
     */
    public function create(GrantRole $entity): GrantRole;

    /**
     * @param Vo\GrantRoleId $id
     * @return GrantRole
     */
    public function read(Vo\GrantRoleId $id): GrantRole;

    /**
     * @param GrantRole $entity
     * @return GrantRole
     */
    public function update(GrantRole $entity): GrantRole;

    /**
     * @param GrantRole $entity
     * @return GrantRole
     */
    public function delete(GrantRole $entity): GrantRole;
}
