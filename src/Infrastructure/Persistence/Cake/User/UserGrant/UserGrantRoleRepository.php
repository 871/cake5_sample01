<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Cake\User\UserGrant;

use App\Domain\User\UserGrant\Entity\GrantRole;
use App\Domain\User\UserGrant\Repository\UserGrantRoleRepository as InterfaceRepo;
use App\Domain\User\UserGrant\ValueObject as Vo;
use Cake\ORM\Query\SelectQuery;
use DateTimeInterface;

final class UserGrantRoleRepository implements InterfaceRepo
{
    public function __construct(private readonly DateTimeInterface $datetime)
    {
    }

    public function query(mixed $condition): SelectQuery
    {
        return (new UserGrantRoleRepository\Query())->run($condition);
    }

    public function search(mixed $condition): array
    {
        return (new UserGrantRoleRepository\Search($this->datetime))->run($condition);
    }

    public function create(GrantRole $entity): GrantRole
    {
        return (new UserGrantRoleRepository\Create($this->datetime))->run($entity);
    }

    public function read(Vo\GrantRoleId $id): GrantRole
    {
        return (new UserGrantRoleRepository\Detail($this->datetime))->run($id);
    }

    public function update(GrantRole $entity): GrantRole
    {
        return (new UserGrantRoleRepository\Update($this->datetime))->run($entity);
    }

    public function delete(GrantRole $entity): GrantRole
    {
        return (new UserGrantRoleRepository\Delete($this->datetime))->run($entity);
    }
}
