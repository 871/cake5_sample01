<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Cake\Admin\AdminGrant\AdminAccountGrantRepository;

use App\Domain\Admin\AdminGrant\Entity\AdminAccountGrant;
use App\Domain\Admin\AdminGrant\ValueObject as Vo;
use App\Model\Table\Admin\AdminAccountsTable;
use App\Lib\UUID\UUID;
use Cake\ORM\Locator\LocatorAwareTrait;
use DateTimeInterface;

final class Save
{
    use LocatorAwareTrait;

    /**
     * @var \App\Model\Table\Admin\AdminAccountsTable
     */
    private AdminAccountsTable $table;

    /**
     * @param \App\Domain\Admin\AdminGrant\Entity\AdminAccountGrant $adminAccountGrant
     */
    public function __construct(
        private readonly AdminAccountGrant $adminAccountGrant,
    ) {
        $this->table = $this->fetchTable(AdminAccountsTable::class);
    }

    /**
     * @return \App\Domain\Admin\AdminGrant\Entity\AdminAccountGrant
     */
    public function run(): AdminAccountGrant
    {
        // TODO 未実装

        
        return $this->adminAccountGrant;
    }
}
