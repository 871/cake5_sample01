<?php
declare(strict_types=1);

namespace App\Domain\Admin\AdminGrant;

use App\Domain\Shared\ValueObject\SearchText;

class SearchAdminGrantRoleCondition
{
    /**
     * @param \App\Domain\Shared\ValueObject\SearchText $searchText
     */
    public function __construct(
        private readonly SearchText $searchText,
    ) {
        // 処理なし
    }

    /**
     * @return \App\Domain\Shared\ValueObject\SearchText
     */
    public function getSearchText(): SearchText
    {
        return $this->searchText;
    }
}
