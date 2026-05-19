<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Cake\Admin\AdminGrant\AdminAccountGrantRepository;

use App\Domain\Admin\AdminGrant\Entity\AdminAccountGrant;
use App\Domain\Admin\AdminGrant\ValueObject as Vo;
use App\Model\Table\Admin\AdminAccountsTable;
use App\Infrastructure\Persistence\Cake\Admin\AdminGrant\AdminAccountGrantMapper;
use Cake\ORM\Locator\LocatorAwareTrait;

final class Detail
{
    use LocatorAwareTrait;

    /**
     * @var \App\Model\Table\Admin\AdminAccountsTable
     */
    private AdminAccountsTable $table;

    /**
     * @param \App\Domain\Admin\AdminGrant\ValueObject\AdminAccountId $adminAccountId
     */
    public function __construct(
        private readonly Vo\AdminAccountId $adminAccountId,
    ) {
        $this->table = $this->fetchTable(AdminAccountsTable::class);
    }

    /**
     * @return \App\Domain\Admin\AdminGrant\Entity\AdminAccountGrant
     */
    public function run(): AdminAccountGrant
    {
        /** @var \App\Model\Entity\Admin\AdminAccount $ormEntity */
        $ormEntity = $this->table->find()
            ->where([
                'AdminAccounts.id' => $this->adminAccountId->toString(),
            ])
            ->contain([
                'AccountStatusMasters',
                'GrantAccountRoles' => [
                    'GrantRoles' => [
                        'GrantRolePermissions' => [
                            'GrantPermissions',
                        ],
                    ],
                ],
                'GrantAccountPermissions' => [
                    'GrantPermissions',
                ],
            ])
            ->firstOrFail();

        return AdminAccountGrantMapper::toAdminAccountGrant($ormEntity);
    }
}
