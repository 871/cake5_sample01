<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Cake\Log\PageAccessLogs\PageAccessLogsRepository;

use App\Domain\Exception\RepositoryException;
use App\Domain\Log\PageAccessLogs\Entity\PageAccessLog as DomainEntity;
use App\Infrastructure\Persistence\Cake\Log\PageAccessLogs\PageAccessLogMapper;
use App\Model\Entity\Log\PageAccessLog as OrmEntity;
use App\Model\Table\Log\PageAccessLogsTable;
use Cake\ORM\Exception\PersistenceFailedException;
use Cake\ORM\Locator\LocatorAwareTrait;

final class Create
{
    use LocatorAwareTrait;

    /**
     * @var \App\Model\Table\Log\PageAccessLogsTable
     */
    private PageAccessLogsTable $table;

    /**
     * @var \App\Infrastructure\Persistence\Cake\Log\PageAccessLogs\PageAccessLogMapper
     */
    private PageAccessLogMapper $mapper;

    /**
     * @param \App\Domain\Log\PageAccessLogs\Entity\PageAccessLog $domainEntity
     */
    public function __construct(
        private readonly DomainEntity $domainEntity,
    ) {
        $this->table = $this->fetchTable(PageAccessLogsTable::class);
        $this->mapper = new PageAccessLogMapper();
    }

    /**
     * @return \App\Domain\Log\PageAccessLogs\Entity\PageAccessLog
     */
    public function run(): DomainEntity
    {
        try {
            /** @var \App\Model\Entity\Log\PageAccessLog $ormEntity */
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
                message: 'PageAccessLogsRepository Create Error',
                previous: $ex,
            );
        }
    }
}
