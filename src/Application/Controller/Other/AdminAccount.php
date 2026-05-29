<?php
declare(strict_types=1);

namespace App\Application\Controller\Other;

use App\Application\Controller\Shared\ServiceInterface;
use App\Application\Controller\Shared\ServiceTrait;
use App\Model\Entity\Shared\AccountStatusMaster;
use App\Model\Table\Shared\AccountStatusMastersTable;
use Cake\ORM\Locator\LocatorAwareTrait;

final class AdminAccount implements ServiceInterface
{
    use ServiceTrait;
    use LocatorAwareTrait;

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getAccountStatusOptions(): array
    {
        /** @var \App\Model\Table\Shared\AccountStatusMastersTable $table */
        $table = $this->fetchTable(AccountStatusMastersTable::class);
        /** @var array<int, array<string, mixed>> */
        return $table->find()
            ->select(['id', 'name'])
            ->where(['is_active' => 1])
            ->orderBy(['sort' => 'ASC'])
            ->all()
            ->map(fn(AccountStatusMaster $e) => ['value' => (string)$e->id, 'label' => $e->name])
            ->toList();
    }
}
