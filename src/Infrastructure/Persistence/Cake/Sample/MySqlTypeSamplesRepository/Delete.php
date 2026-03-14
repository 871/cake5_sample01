<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Cake\Sample\MySqlTypeSamplesRepository;

use App\Domain\Sample\MySqlTypeSamples\Entity\MySqlTypeSample as DomainEntity;
use App\Domain\Sample\MySqlTypeSamples\ValueObject as Vo;
use App\Infrastructure\Persistence\Cake\Sample\MySqlTypeSampleMapper;
use App\Model\Table\Sample\MySqlTypeSamplesTable;
use Cake\ORM\Locator\LocatorAwareTrait;

final class Delete
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
     * @param \App\Domain\Sample\MySqlTypeSamples\ValueObject\Id $id
     */
    public function __construct(
        private readonly Vo\Id $id,
    ) {
        $this->table = $this->fetchTable(MySqlTypeSamplesTable::class);
        $this->mapper = new MySqlTypeSampleMapper();
    }

    /**
     * @return \App\Domain\Sample\MySqlTypeSamples\Entity\MySqlTypeSample
     */
    public function run(): DomainEntity
    {
        /** @var \App\Model\Entity\Sample\MySqlTypeSample $ormEntity */
        $ormEntity = $this->table->get($this->id->toString());

        $this->table->deleteOrFail($ormEntity);

        return $this->mapper->toDomainEntity($ormEntity);
    }
}
