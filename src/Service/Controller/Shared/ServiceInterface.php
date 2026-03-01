<?php
declare(strict_types=1);

namespace App\Service\Controller\Shared;

use App\Security\Auth\AuthContext;
use Cake\Http\ServerRequest;
use DateTimeInterface;

interface ServiceInterface
{
    /**
     * @param DateTimeInterface $datetime
     * @param ServerRequest $request
     * @param AuthContext $authContext
     */
    public function __construct(
        DateTimeInterface $datetime,
        ServerRequest $request,
        AuthContext $authContext,
    );

    /**
     * @param string $serviceClassName
     * @return \App\Service\Controller\Shared\ServiceInterface
     */
    public function createService(string $serviceClassName): ServiceInterface;
}
