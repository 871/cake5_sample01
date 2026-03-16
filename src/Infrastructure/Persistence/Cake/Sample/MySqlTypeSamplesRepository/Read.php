<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Cake\Sample\MySqlTypeSamplesRepository;

use App\Domain\Exception\RepositoryException;
use App\Domain\Sample\MySqlTypeSamples\Entity\MySqlTypeSample as DomainEntity;
use App\Domain\Sample\MySqlTypeSamples\ValueObject as Vo;
use App\Infrastructure\Persistence\Cake\Sample\MySqlTypeSampleMapper;
use App\Model\Entity\Sample\MySqlTypeSample as OrmEntity;
use App\Model\Table\Sample\MySqlTypeSamplesTable;
use Cake\ORM\Locator\LocatorAwareTrait;

final class Read
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
        /** @var \App\Domain\Sample\MySqlTypeSamples\Entity\MySqlTypeSample */
        return $this->table
            ->find()
            ->select([
                'MySqlTypeSamples__id' => 'MySqlTypeSamples.id',
                'MySqlTypeSamples__int_col' => 'MySqlTypeSamples.int_col',
                'MySqlTypeSamples__bigint_col' => 'MySqlTypeSamples.bigint_col',
                'MySqlTypeSamples__decimal_col' => 'MySqlTypeSamples.decimal_col',
                'MySqlTypeSamples__float_col' => 'MySqlTypeSamples.float_col',
                'MySqlTypeSamples__double_col' => 'MySqlTypeSamples.double_col',
                'MySqlTypeSamples__date_col' => 'MySqlTypeSamples.date_col',
                'MySqlTypeSamples__time_col' => 'MySqlTypeSamples.time_col',
                'MySqlTypeSamples__datetime_col' => 'MySqlTypeSamples.datetime_col',
                'MySqlTypeSamples__char_col' => 'MySqlTypeSamples.char_col',
                'MySqlTypeSamples__varchar_col' => 'MySqlTypeSamples.varchar_col',
                'MySqlTypeSamples__text_col' => 'MySqlTypeSamples.text_col',
                'MySqlTypeSamples__mediumtext_col' => 'MySqlTypeSamples.mediumtext_col',
                'MySqlTypeSamples__longtext_col' => 'MySqlTypeSamples.longtext_col',
                'MySqlTypeSamples__json_col' => 'MySqlTypeSamples.json_col',
            ])
            ->where([
                'MySqlTypeSamples.id' => $this->id->toString(),
            ])
            ->formatResults(function ($results) {
                return $results->map(function (OrmEntity $entity) {

                    return $this->mapper->toDomainEntity($entity);
                });
            })
            ->first() ?? throw new RepositoryException(
                'MySqlTypeSample data not fund'
                . '[id: ' . $this->id->toString() . ']',
            );
    }
}
