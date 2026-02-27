<?php
declare(strict_types=1);

namespace App\Service\Controller\Shared;

interface ServiceInterface
{
    /**
     * 
     */
    public function __construct(
        \DateTimeInterface $datetime,
        \Cake\Http\ServerRequest $request,
        \App\Security\Auth\AuthContext $authContext,
    );

    /**
     * 
     * @param string $serviceClassName
     * @return ServiceInterface
     */
    public function createService(string $serviceClassName) : ServiceInterface;
}