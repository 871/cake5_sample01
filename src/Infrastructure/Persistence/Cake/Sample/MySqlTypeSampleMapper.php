<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Cake\Sample;

use App\Domain\Sample\MySqlTypeSamples\Entity\MySqlTypeSample as DomainEntity;
use App\Lib\UUID\UUID;
use App\Model\Entity\Sample\MySqlTypeSample as OrmEntity;
use App\Model\Table\Sample\MySqlTypeSamplesTable;
use App\Security\Input\Cast;
use Cake\ORM\Locator\LocatorAwareTrait;

final class MySqlTypeSampleMapper
{
    use LocatorAwareTrait;

    /**
     * @var \App\Model\Table\Sample\MySqlTypeSamplesTable
     */
    private MySqlTypeSamplesTable $table;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->table = $this->fetchTable(MySqlTypeSamplesTable::class);
    }

    /**
     * @param \App\Domain\Sample\MySqlTypeSamples\Entity\MySqlTypeSample $domainEntity
     * @return \App\Model\Entity\Sample\MySqlTypeSample
     */
    public function toNewOrmEntity(DomainEntity $domainEntity): OrmEntity
    {
        return $this->table->newEntity([
            'id' => UUID::uuid7(),
            'int_col' => $domainEntity->intCol()->toString(),
            'bigint_col' => $domainEntity->bigintCol()->toString(),
            'decimal_col' => $domainEntity->decimalCol()->toString(),
            'float_col' => $domainEntity->floatCol()->toString(),
            'double_col' => $domainEntity->doubleCol()->toString(),
            'date_col' => $domainEntity->dateCol()->toString(),
            'time_col' => $domainEntity->timeCol()->toString(),
            'datetime_col' => $domainEntity->datetimeCol()->toString(),
            'char_col' => $domainEntity->charCol()->toString(),
            'varchar_col' => $domainEntity->varcharCol()->toString(),
            'text_col' => $domainEntity->textCol()->toString(),
            'mediumtext_col' => $domainEntity->mediumtextCol()->toString(),
            'longtext_col' => $domainEntity->longtextCol()->toString(),
            'json_col' => json_decode($domainEntity->jsonCol()->toString(), true),
        ], [
            'validate' => false,
        ]);
    }

    /**
     * @param \App\Domain\Sample\MySqlTypeSamples\Entity\MySqlTypeSample $domainEntity
     * @return \App\Model\Entity\Sample\MySqlTypeSample
     */
    public function toPatchOrmEntity(DomainEntity $domainEntity): OrmEntity
    {
        $ormEntity = $this->table->get($domainEntity->id()->toString());

        $this->table->patchEntity($ormEntity, [
            'int_col' => $domainEntity->intCol()->toString(),
            'bigint_col' => $domainEntity->bigintCol()->toString(),
            'decimal_col' => $domainEntity->decimalCol()->toString(),
            'float_col' => $domainEntity->floatCol()->toString(),
            'double_col' => $domainEntity->doubleCol()->toString(),
            'date_col' => $domainEntity->dateCol()->toString(),
            'time_col' => $domainEntity->timeCol()->toString(),
            'datetime_col' => $domainEntity->datetimeCol()->toString(),
            'char_col' => $domainEntity->charCol()->toString(),
            'varchar_col' => $domainEntity->varcharCol()->toString(),
            'text_col' => $domainEntity->textCol()->toString(),
            'mediumtext_col' => $domainEntity->mediumtextCol()->toString(),
            'longtext_col' => $domainEntity->longtextCol()->toString(),
            'json_col' => json_decode($domainEntity->jsonCol()->toString(), true),
        ], [
            'validate' => false,
        ]);

        return $ormEntity;
    }

    /**
     * @param \App\Model\Entity\Sample\MySqlTypeSample $ormEntity
     * @return \App\Domain\Sample\MySqlTypeSamples\Entity\MySqlTypeSample
     */
    public function toDomainEntity(OrmEntity $ormEntity): DomainEntity
    {
        return new DomainEntity(
            id: $ormEntity->id,
            int_col: Cast::toString($ormEntity->int_col),
            bigint_col: Cast::toString($ormEntity->bigint_col),
            decimal_col: Cast::toString($ormEntity->decimal_col),
            float_col: Cast::toString($ormEntity->float_col),
            double_col: Cast::toString($ormEntity->double_col),
            date_col: Cast::toString($ormEntity->date_col?->format('Y-m-d')),
            time_col: Cast::toString($ormEntity->time_col?->format('H:i:s')),
            datetime_col: Cast::toString($ormEntity->datetime_col?->format('Y-m-d H:i:s')),
            char_col: Cast::toString($ormEntity->char_col),
            varchar_col: Cast::toString($ormEntity->varchar_col),
            text_col: Cast::toString($ormEntity->text_col),
            mediumtext_col: Cast::toString($ormEntity->mediumtext_col),
            longtext_col: Cast::toString($ormEntity->longtext_col),
            json_col: json_encode($ormEntity->json_col) ?: null,
        );
    }
}
