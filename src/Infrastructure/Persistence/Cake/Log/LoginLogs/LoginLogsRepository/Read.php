<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Cake\Log\LoginLogs\LoginLogsRepository;

use App\Domain\Exception\RepositoryException;
use App\Domain\Log\LoginLogs\Entity\LoginLog as DomainEntity;
use App\Domain\Log\LoginLogs\ValueObject as Vo;
use App\Infrastructure\Persistence\Cake\Log\LoginLogMapper;
use App\Model\Table\Log\LoginLogsTable;
use Cake\ORM\Locator\LocatorAwareTrait;

final class Read
{
    use LocatorAwareTrait;

    /**
     * @var \App\Model\Table\Log\LoginLogsTable
     */
    private LoginLogsTable $table;

    /**
     * @var \App\Infrastructure\Persistence\Cake\Log\LoginLogMapper
     */
    private LoginLogMapper $mapper;

    /**
     * @param \App\Domain\Log\LoginLogs\ValueObject\Id $id
     */
    public function __construct(
        private readonly Vo\Id $id,
    ) {
        $this->table = $this->fetchTable(LoginLogsTable::class);
        $this->mapper = new LoginLogMapper();
    }

    /**
     * @return \App\Domain\Log\LoginLogs\Entity\LoginLog
     */
    public function run(): DomainEntity
    {
        /** @var \App\Model\Entity\Log\LoginLog $ormEntity */
        $ormEntity = $this->table
            ->find()
            ->contain([
                // 'UserAccounts',
                'AdminAccounts',
                'ImpersonatorAdminAccounts',
            ])
            ->where([
                'LoginLogs.id' => $this->id->toString(),
            ])
            ->first() ?? throw new RepositoryException(
                'LoginLog data not found'
                . '[id: ' . $this->id->toString() . ']',
            );

        return $this->mapper->toDomainEntity($ormEntity);
    }
}
