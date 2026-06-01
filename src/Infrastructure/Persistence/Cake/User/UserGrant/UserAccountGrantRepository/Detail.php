<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Cake\User\UserGrant\UserAccountGrantRepository;

use App\Domain\User\UserGrant\Entity\UserAccountGrant;
use App\Domain\User\UserGrant\ValueObject as Vo;
use App\Model\Table\User\UserAccountsTable;
use Cake\ORM\Locator\LocatorAwareTrait;

final class Detail
{
    use LocatorAwareTrait;

    /**
     * @var \App\Model\Table\User\UserAccountsTable
     */
    private UserAccountsTable $table;

    /**
     * コンストラクタ
     */
    public function __construct()
    {
        $this->table = $this->fetchTable(UserAccountsTable::class);
    }

    /**
     * @return \App\Domain\User\UserGrant\Entity\UserAccountGrant
     */
    public function run(Vo\UserAccountId $userAccountId): UserAccountGrant
    {
        /** @var \App\Model\Entity\User\UserAccount $ormEntity */
        $ormEntity = $this->table->find()
            ->where([
                'UserAccounts.id' => $userAccountId->toString(),
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

        return FromOrmToDomainMapper::toUserAccountGrant($ormEntity);
    }
}
