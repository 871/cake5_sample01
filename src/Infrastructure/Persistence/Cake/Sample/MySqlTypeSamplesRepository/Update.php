<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Cake\Sample\MySqlTypeSamplesRepository;

use App\Domain\Exception\RepositoryException;
use App\Domain\Sample\MySqlTypeSamples\Entity\MySqlTypeSample as DomainEntity;
use App\Model\Entity\Sample\MySqlTypeSample as OrmEntity;
use App\Model\Table\Sample\MySqlTypeSamplesTable;
use App\Security\Input\Cast;
use Cake\ORM\Exception\PersistenceFailedException;
use Cake\ORM\Locator\LocatorAwareTrait;

final class Update
{
    use LocatorAwareTrait;

    /**
     * @var \App\Model\Table\Sample\MySqlTypeSamplesTable
     */
    private MySqlTypeSamplesTable $table;

    /**
     * @param \App\Domain\Sample\MySqlTypeSamples\Entity\MySqlTypeSample $domainEntity
     */
    public function __construct(
        private readonly DomainEntity $domainEntity,
    ) {
        $this->table = $this->fetchTable(MySqlTypeSamplesTable::class);
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
                    $entity = $this->table->get($this->domainEntity->id()->toString());

                    $this->patchOrmEntity($entity);

                    return $this->table->saveOrFail($entity, [
                        'checkExisting' => false,
                    ]);
                },
            );
        } catch (PersistenceFailedException $ex) {
            throw new RepositoryException(
                message: 'MySqlTypeSamplesRepository Update Error',
                previous: $ex,
            );
        }

        return $this->convertDomainEntity($ormEntity);
    }

    /**
     * @return \App\Model\Entity\Sample\MySqlTypeSample $ormEntity
     * @return void
     */
    private function patchOrmEntity(OrmEntity $ormEntity): void
    {
        $this->table->patchEntity($ormEntity, [
            'int_col' => $this->domainEntity->intCol()->toString(),
            'bigint_col' => $this->domainEntity->bigintCol()->toString(),
            'decimal_col' => $this->domainEntity->decimalCol()->toString(),
            'float_col' => $this->domainEntity->floatCol()->toString(),
            'double_col' => $this->domainEntity->doubleCol()->toString(),
            'date_col' => $this->domainEntity->dateCol()->toString(),
            'time_col' => $this->domainEntity->timeCol()->toString(),
            'datetime_col' => $this->domainEntity->datetimeCol()->toString(),
            'char_col' => $this->domainEntity->charCol()->toString(),
            'varchar_col' => $this->domainEntity->varcharCol()->toString(),
            'text_col' => $this->domainEntity->textCol()->toString(),
            'mediumtext_col' => $this->domainEntity->mediumtextCol()->toString(),
            'longtext_col' => $this->domainEntity->longtextCol()->toString(),
            'json_col' => json_decode($this->domainEntity->jsonCol()->toString(), true),
        ]);
    }

    /**
     * @param \App\Model\Entity\Sample\MySqlTypeSample $ormEntity
     * @return \App\Domain\Sample\MySqlTypeSamples\Entity\MySqlTypeSample
     */
    private function convertDomainEntity(OrmEntity $ormEntity): DomainEntity
    {
        return new DomainEntity(
            id: $ormEntity->id,
            int_col: Cast::toString($ormEntity->int_col),
            bigint_col: Cast::toString($ormEntity->bigint_col),
            decimal_col: $ormEntity->decimal_col,
            float_col: (string)$ormEntity->float_col,
            double_col: (string)$ormEntity->double_col,
            date_col: $ormEntity->date_col?->format('Y-m-d'),
            time_col: $ormEntity->time_col?->format('H:i:s'),
            datetime_col: $ormEntity->datetime_col?->format('Y-m-d H:i:s'),
            char_col: $ormEntity->char_col,
            varchar_col: $ormEntity->varchar_col,
            text_col: $ormEntity->text_col,
            mediumtext_col: $ormEntity->mediumtext_col,
            longtext_col: $ormEntity->longtext_col,
            json_col: json_encode($ormEntity->json_col) ?: null,
        );
    }
}
