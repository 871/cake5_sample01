<?php
declare(strict_types=1);

namespace App\Service\Controller;

use App\Service\Controller\Shared\ServiceInterface;
use App\Service\Controller\Shared\ServiceTrait;

final class Sample implements ServiceInterface
{
    use ServiceTrait;
}
