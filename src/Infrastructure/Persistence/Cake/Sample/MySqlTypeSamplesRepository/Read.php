<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Cake\Sample\MySqlTypeSamplesRepository;

use App\Domain\Sample\MySqlTypeSamples\Entity\MySqlTypeSample as DomainEntity;
use App\Domain\Sample\MySqlTypeSamples\ValueObject as Vo;
use App\Model\Entity\Sample\MySqlTypeSample as OrmEntity;
use App\Model\Table\Sample\MySqlTypeSamplesTable;
use Cake\ORM\Locator\LocatorAwareTrait;
use DomainException;

final class Read
{
    use LocatorAwareTrait;

    /**
     * @var \App\Model\Table\Sample\MySqlTypeSamplesTable
     */
    private MySqlTypeSamplesTable $table;

    /**
     * @param \App\Domain\Sample\MySqlTypeSamples\ValueObject\Id $id
     */
    public function __construct(
        private readonly Vo\Id $id,
    ) {
        $this->table = $this->fetchTable(MySqlTypeSamplesTable::class);
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
                    // Memo: 実装的にはやりすぎ感はあるがTEST的に試す
                    return new DomainEntity(
                        id: $entity->id === null ? null : (string)$entity->id,
                        int_col: $entity->int_col === null ? null : (string)$entity->int_col,
                        bigint_col: $entity->bigint_col === null ? null : (string)$entity->bigint_col,
                        decimal_col: $entity->decimal_col === null ? null : (string)$entity->decimal_col,
                        float_col: $entity->float_col === null ? null : (string)$entity->float_col,
                        double_col: $entity->double_col === null ? null : (string)$entity->double_col,
                        date_col: $entity->date_col?->format('Y-m-d'),
                        time_col: $entity->time_col?->format('H:i:s'),
                        datetime_col: $entity->datetime_col?->format('Y-m-d\TH:i:s'),
                        char_col: $entity->char_col,
                        varchar_col: $entity->varchar_col,
                        text_col: $entity->text_col,
                        mediumtext_col: $entity->mediumtext_col,
                        longtext_col: $entity->longtext_col,
                        json_col: $entity->json_col === null
                            ? null
                            : json_encode($entity->json_col, JSON_THROW_ON_ERROR),
                    );
                });
            })
            ->first() ?? throw new DomainException(
                'MySqlTypeSample data not fund'
                . '[id: ' . $this->id->toString() . ']',
            );
    }
}
