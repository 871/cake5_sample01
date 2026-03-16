<?php
declare(strict_types=1);

namespace App\Domain\Admin\AdminRoles\Entity;

use App\Domain\Admin\AdminRoles\ValueObject;

final class AdminRole
{
    /**
     * @param ?string $id
     * @param ?string $name
     */
    public function __construct(
        private readonly ?string $id,
        private readonly ?string $name,
    ) {
    }

    /**
     * @return \App\Domain\Admin\AdminRoles\ValueObject\Id
     */
    public function id(): ValueObject\Id
    {
        return ValueObject\Id::fromString($this->id);
    }

    /**
     * @return \App\Domain\Admin\AdminRoles\ValueObject\Name
     */
    public function name(): ValueObject\Name
    {
        return ValueObject\Name::fromString($this->name);
    }
}
