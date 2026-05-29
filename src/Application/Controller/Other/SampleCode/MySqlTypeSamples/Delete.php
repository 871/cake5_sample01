<?php
declare(strict_types=1);

namespace App\Application\Controller\Other\SampleCode\MySqlTypeSamples;

use App\Application\Controller\Shared\ServiceInterface;
use App\Application\Controller\Shared\ServiceTrait;
use App\Domain\Sample\MySqlTypeSamples\Entity\MySqlTypeSample;
use App\Domain\Sample\MySqlTypeSamples\ValueObject as Vo;
use App\Infrastructure\Persistence\Cake\Sample\MySqlTypeSamplesRepository;
use App\Security\Input\StrictCast;

final class Delete implements ServiceInterface
{
    use ServiceTrait;

    /**
     * @return \App\Domain\Sample\MySqlTypeSamples\Entity\MySqlTypeSample
     */
    public function delete(): MySqlTypeSample
    {
        return (new MySqlTypeSamplesRepository())->delete(
            new Vo\Id(
                StrictCast::toString($this->request->getParam('my_sql_type_sample_id')),
            ),
        );
    }
}
