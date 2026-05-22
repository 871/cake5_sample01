<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Cake\Admin\AdminGrant\AdminAccountGrantRepository;

use App\Model\Entity\Shared\AccountStatusMaster as OrmEntity;
use App\Model\Table\Shared\AccountStatusMastersTable;
use Cake\ORM\Locator\LocatorAwareTrait;

final class FindAccountStatusMasters
{
    use LocatorAwareTrait;

    /**
     * @var \App\Model\Table\Shared\AccountStatusMastersTable
     */
    private AccountStatusMastersTable $table;

    /**
     * コンストラクタ
     */
    public function __construct()
    {
        $this->table = $this->fetchTable(AccountStatusMastersTable::class);
    }

    /**
     * @return array<\App\Domain\Admin\AdminGrant\Entity\AccountStatusMaster>
     */
    public function run(): array
    {
        return array_map(function (OrmEntity $ormEntity) {
            return FromOrmToDomainMapper::toAccountStatusMaster($ormEntity);
        }, $this->table
            ->find()
            ->orderBy([
                'AccountStatusMasters.sort' => 'ASC',
                'AccountStatusMasters.id' => 'ASC',
            ])
            ->all()
            ->toArray());
    }
}
