<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Cake\Log\LoginLogs\LoginLogsRepository;

use App\Domain\Exception\RepositoryException;
use App\Domain\Log\LoginLogs\Entity\LoginLog as DomainEntity;
use App\Infrastructure\Persistence\Cake\Log\LoginLogs\LoginLogMapper;
use App\Model\Entity\Log\LoginLog as OrmEntity;
use App\Model\Table\Log\LoginLogsTable;
use Cake\ORM\Exception\PersistenceFailedException;
use Cake\ORM\Locator\LocatorAwareTrait;

final class Create
{
    use LocatorAwareTrait;

    /**
     * @var \App\Model\Table\Log\LoginLog\LoginLogsTable
     */
    private LoginLogsTable $table;

    /**
     * @var \App\Infrastructure\Persistence\Cake\Log\LoginLogs\LoginLogMapper
     */
    private LoginLogMapper $mapper;

    /**
     * @param \App\Domain\Log\LoginLogs\Entity\LoginLog $domainEntity
     */
    public function __construct(
        private readonly DomainEntity $domainEntity,
    ) {
        $this->table = $this->fetchTable(LoginLogsTable::class);
        $this->mapper = new LoginLogMapper();
    }

    /**
     * @return \App\Domain\Log\LoginLogs\Entity\LoginLog
     */
    public function run(): DomainEntity
    {
        try {
            /** @var \App\Model\Entity\Log\LoginLog $ormEntity */
            $ormEntity = $this->table->getConnection()->transactional(
                function (): OrmEntity {
                    $savedEntity = $this->table->saveOrFail(
                        $this->mapper->toNewOrmEntity($this->domainEntity),
                        [
                            'checkExisting' => false,
                        ],
                    );

                    return $savedEntity;
                },
            );

            return $this->mapper->toDomainEntity($ormEntity);
        } catch (PersistenceFailedException $ex) {
            throw new RepositoryException(
                message: 'LoginLogsRepository Create Error',
                previous: $ex,
            );
        }
    }
}
