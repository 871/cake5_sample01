<?php
declare(strict_types=1);

namespace App\Service\Controller\SampleCode\MySqlTypeSamples;

use App\Domain\Sample\MySqlTypeSamples\Entity\MySqlTypeSample as DomainEntity;
use App\Domain\Sample\MySqlTypeSamples\ValueObject as Vo;
use App\Infrastructure\Persistence\Cake\Sample\MySqlTypeSamplesRepository;
use App\Security\Input\StrictCast;
use App\Service\Controller\Shared\ServiceInterface;
use App\Service\Controller\Shared\ServiceTrait;

final class Detail implements ServiceInterface
{
    use ServiceTrait;

    /**
     * @return \App\Domain\Sample\MySqlTypeSamples\Entity\MySqlTypeSample
     */
    public function getDomainEntity(): DomainEntity
    {
        return (new MySqlTypeSamplesRepository())->read(
            new Vo\Id(
                StrictCast::toString($this->request->getParam('my_sql_type_sample_id')),
            ),
        );
    }
}
