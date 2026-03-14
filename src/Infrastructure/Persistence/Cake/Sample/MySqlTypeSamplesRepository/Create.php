<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Cake\Sample\MySqlTypeSamplesRepository;

use App\Domain\Exception\RepositoryException;
use App\Domain\Sample\MySqlTypeSamples\Entity\MySqlTypeSample as DomainEntity;
use App\Infrastructure\Persistence\Cake\Sample\MySqlTypeSampleMapper;
use App\Model\Entity\Sample\MySqlTypeSample as OrmEntity;
use App\Model\Table\Sample\MySqlTypeSamplesTable;
use Cake\ORM\Exception\PersistenceFailedException;
use Cake\ORM\Locator\LocatorAwareTrait;

final class Create
{
    use LocatorAwareTrait;

    /**
     * @var \App\Model\Table\Sample\MySqlTypeSamplesTable
     */
    private MySqlTypeSamplesTable $table;

    /**
     * @var \App\Infrastructure\Persistence\Cake\Sample\MySqlTypeSampleMapper
     */
    private MySqlTypeSampleMapper $mapper;

    /**
     * @param \App\Domain\Sample\MySqlTypeSamples\Entity\MySqlTypeSample $domainEntity
     */
    public function __construct(
        private readonly DomainEntity $domainEntity,
    ) {
        $this->table = $this->fetchTable(MySqlTypeSamplesTable::class);
        $this->mapper = new MySqlTypeSampleMapper();
    }

    /**
     * @return \App\Domain\Sample\MySqlTypeSamples\Entity\MySqlTypeSample
     */
    public function run(): DomainEntity
    {
        try {
            /** @var \App\Model\Entity\Sample\MySqlTypeSample $ormEntity */
            $ormEntity = $this->table->getConnection()->transactional(
                function (): OrmEntity {

                    return $this->table->saveOrFail(
                        $this->mapper->toNewOrmEntity($this->domainEntity),
                        [
                            'checkExisting' => false,
                        ],
                    );
                },
            );

            return $this->mapper->toDomainEntity($ormEntity);
        } catch (PersistenceFailedException $ex) {
            throw new RepositoryException(
                message: 'MySqlTypeSamplesRepository Create Error',
                previous: $ex,
            );
        }
    }
}
