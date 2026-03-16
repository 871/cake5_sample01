<?php
declare(strict_types=1);

namespace App\Service\Controller;

use App\Model\Table\Shared\AccountStatusMastersTable;
use App\Service\Controller\Shared\ServiceInterface;
use App\Service\Controller\Shared\ServiceTrait;
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
            ->map(fn($e) => ['value' => (string)$e->id, 'label' => $e->name])
            ->toList();
    }
}
