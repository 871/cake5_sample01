<?php
declare(strict_types=1);

namespace App\Domain\Admin\AdminGrant\Entity;

use App\Domain\Admin\AdminGrant\ValueObject as Vo;
use App\Domain\Shared\ValueObject as SVo;

final class GrantPermission
{
    /**
     * @param ?string $id
     * @param ?string $code
     * @param ?string $name
     * @param ?string $description
     * @param ?string $sort
     * @param ?string $is_active
     * @param ?string $created
     * @param ?string $modified
     */
    public function __construct(
        private readonly ?string $id,
        private readonly ?string $code,
        private readonly ?string $name,
        private readonly ?string $description,
        private readonly ?string $sort,
        private readonly ?string $is_active,
        private readonly ?string $created,
        private readonly ?string $modified,
    ) {
    }

    /**
     * @return \App\Domain\Admin\AdminGrant\ValueObject\GrantPermissionId
     */
    public function id(): Vo\GrantPermissionId
    {
        return new Vo\GrantPermissionId($this->id);
    }

    /**
     * @return \App\Domain\Admin\AdminGrant\ValueObject\Code
     */
    public function code(): Vo\Code
    {
        return new Vo\Code($this->code);
    }

    /**
     * @return \App\Domain\Admin\AdminGrant\ValueObject\Name
     */
    public function name(): Vo\Name
    {
        return new Vo\Name($this->name);
    }

    /**
     * @return \App\Domain\Admin\AdminGrant\ValueObject\Description
     */
    public function description(): Vo\Description
    {
        return new Vo\Description($this->description);
    }

    /**
     * @return \App\Domain\Admin\AdminGrant\ValueObject\Sort
     */
    public function sort(): Vo\Sort
    {
        return new Vo\Sort($this->sort);
    }

    /**
     * @return \App\Domain\Admin\AdminGrant\ValueObject\IsActive
     */
    public function isActive(): Vo\IsActive
    {
        return new Vo\IsActive($this->is_active);
    }

    /**
     * @return \App\Domain\Shared\ValueObject\Created
     */
    public function created(): SVo\Created
    {
        return new SVo\Created($this->created);
    }

    /**
     * @return \App\Domain\Shared\ValueObject\Modified
     */
    public function modified(): SVo\Modified
    {
        return new SVo\Modified($this->modified);
    }
}
