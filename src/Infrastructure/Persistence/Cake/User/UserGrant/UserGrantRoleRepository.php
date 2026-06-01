<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Cake\User\UserGrant;

use App\Domain\User\UserGrant\Entity\GrantRole;
use App\Domain\User\UserGrant\Repository\UserGrantRoleRepository as InterfaceRepo;
use App\Domain\User\UserGrant\SearchUserGrantRoleCondition;
use App\Domain\User\UserGrant\ValueObject as Vo;
use Cake\ORM\Query\SelectQuery;
use DateTimeInterface;

final class UserGrantRoleRepository implements InterfaceRepo
{
    /**
     * @param \DateTimeInterface $datetime
     */
    public function __construct(private readonly DateTimeInterface $datetime)
    {
    }

    /**
     * @param \App\Domain\User\UserGrant\SearchUserGrantRoleCondition $condition
     * @return \Cake\ORM\Query\SelectQuery<\App\Model\Entity\Grant\GrantRole>
     */
    public function query(SearchUserGrantRoleCondition $condition): SelectQuery
    {
        return (new UserGrantRoleRepository\Query())->run($condition);
    }

    /**
     * @param \App\Domain\User\UserGrant\SearchUserGrantRoleCondition $condition
     * @return array<\App\Domain\User\UserGrant\Entity\GrantRole>
     */
    public function search(SearchUserGrantRoleCondition $condition): array
    {
        return (new UserGrantRoleRepository\Search($this->datetime))->run($condition);
    }

    /**
     * @param \App\Domain\User\UserGrant\Entity\GrantRole $entity
     * @return \App\Domain\User\UserGrant\Entity\GrantRole
     */
    public function create(GrantRole $entity): GrantRole
    {
        return (new UserGrantRoleRepository\Create($this->datetime))->run($entity);
    }

    /**
     * @param \App\Domain\User\UserGrant\ValueObject\GrantRoleId $id
     * @return \App\Domain\User\UserGrant\Entity\GrantRole
     */
    public function read(Vo\GrantRoleId $id): GrantRole
    {
        return (new UserGrantRoleRepository\Detail($this->datetime))->run($id);
    }

    /**
     * @param \App\Domain\User\UserGrant\Entity\GrantRole $entity
     * @return \App\Domain\User\UserGrant\Entity\GrantRole
     */
    public function update(GrantRole $entity): GrantRole
    {
        return (new UserGrantRoleRepository\Update($this->datetime))->run($entity);
    }

    /**
     * @param \App\Domain\User\UserGrant\Entity\GrantRole $entity
     * @return \App\Domain\User\UserGrant\Entity\GrantRole
     */
    public function delete(GrantRole $entity): GrantRole
    {
        return (new UserGrantRoleRepository\Delete($this->datetime))->run($entity);
    }
}
