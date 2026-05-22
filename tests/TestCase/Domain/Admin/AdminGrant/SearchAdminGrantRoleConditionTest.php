<?php
declare(strict_types=1);

namespace App\Test\TestCase\Domain\Admin\AdminGrant;

use App\Domain\Admin\AdminGrant\SearchAdminGrantRoleCondition;
use App\Domain\Admin\AdminGrant\ValueObject\GrantPermissionId;
use App\Domain\Admin\AdminGrant\ValueObject\IsActive;
use App\Domain\Shared\ValueObject\SearchText;
use PHPUnit\Framework\TestCase;

final class SearchAdminGrantRoleConditionTest extends TestCase
{
    public function testCanHoldMultipleIsActiveAndPermissionConditions(): void
    {
        $condition = new SearchAdminGrantRoleCondition(
            searchText: new SearchText('role keyword'),
            isActives: [
                new IsActive('1'),
                new IsActive('0'),
            ],
            grantPermissionIds: [
                new GrantPermissionId('10'),
                new GrantPermissionId('20'),
            ],
        );

        $this->assertSame('role keyword', $condition->getSearchText()->toString());
        $this->assertSame([1, 0], array_map(
            static fn(IsActive $isActive): int => $isActive->toInt(),
            $condition->getIsActives(),
        ));
        $this->assertSame([10, 20], array_map(
            static fn(GrantPermissionId $grantPermissionId): int => $grantPermissionId->toInt(),
            $condition->getGrantPermissionIds(),
        ));
    }
}
