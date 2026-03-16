<?php
declare(strict_types=1);

namespace App\Domain\Admin\AdminAccounts;

class SearchCondition
{
    /**
     * @param \App\Domain\Admin\AdminAccounts\ValueObject\Search\Keyword $keyword
     * @param \App\Domain\Admin\AdminAccounts\ValueObject\RoleId $roleId
     * @param \App\Domain\Admin\AdminAccounts\ValueObject\Status $status
     */
    public function __construct(
        private readonly ValueObject\Search\Keyword $keyword,
        private readonly ValueObject\RoleId $roleId,
        private readonly ValueObject\Status $status,
    ) {
        // 処理なし
    }

    /**
     * @return \App\Domain\Admin\AdminAccounts\ValueObject\Search\Keyword
     */
    public function getKeyword(): ValueObject\Search\Keyword
    {
        return $this->keyword;
    }

    /**
     * @return \App\Domain\Admin\AdminAccounts\ValueObject\RoleId
     */
    public function getRoleId(): ValueObject\RoleId
    {
        return $this->roleId;
    }

    /**
     * @return \App\Domain\Admin\AdminAccounts\ValueObject\Status
     */
    public function getStatus(): ValueObject\Status
    {
        return $this->status;
    }
}
