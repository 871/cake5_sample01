<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Cake\Admin\AdminGrant\AdminAccountGrantRepository;

use App\Domain\Admin\AdminGrant\Entity\AdminAccountGrant;
use App\Domain\Admin\AdminGrant\ValueObject as Vo;
use App\Model\Table\Admin\AdminAccountsTable;
use Cake\ORM\Locator\LocatorAwareTrait;
use DateTimeInterface;

final class Detail
{
    use LocatorAwareTrait;

    /**
     * @var \App\Model\Table\Admin\AdminAccountsTable
     */
    private AdminAccountsTable $table;

    /**
     * コンストラクタ
     */
    public function __construct() 
    {
        $this->table = $this->fetchTable(AdminAccountsTable::class);
    }

    /**
     * @return \App\Domain\Admin\AdminGrant\Entity\AdminAccountGrant
     */
    public function run(Vo\AdminAccountId $adminAccountId): AdminAccountGrant
    {
        /** @var \App\Model\Entity\Admin\AdminAccount $ormEntity */
        $ormEntity = $this->table->find()
            ->where([
                'AdminAccounts.id' => $adminAccountId->toString(),
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

        return FromOrmToDomainMapper::toAdminAccountGrant($ormEntity);
    }
}
