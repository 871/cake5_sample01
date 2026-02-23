<?php
declare(strict_types=1);

namespace App\Service\Controller\Shared;

interface ServiceInterface
{
    /**
     * 
     */
    public function __construct(ServiceParamsInterface $params);

    /**
     * 
     * @param string $serviceClassName
     * @return ServiceInterface
     */
    public function createService(string $serviceClassName) : ServiceInterface;
}