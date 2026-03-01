<?php
declare(strict_types=1);

namespace App\Service\Controller\Shared;

use App\Security\Auth\AuthContext;
use Cake\Http\ServerRequest;
use DateTimeInterface;
use InvalidArgumentException;

trait ServiceTrait
{
    /**
     * @param \DateTimeInterface $datetime
     * @param \Cake\Http\ServerRequest $request
     * @param \App\Security\Auth\AuthContext $authContext
     */
    public function __construct(
        private readonly DateTimeInterface $datetime,
        private readonly ServerRequest $request,
        private readonly AuthContext $authContext,
    ) {
        // 処理なし
    }

    /**
     * @param string $serviceClassName
     * @return \App\Service\Controller\Shared\ServiceInterface
     */
    public function createService(string $serviceClassName): ServiceInterface
    {
        return is_subclass_of($serviceClassName, ServiceInterface::class)
            ? new $serviceClassName(
                datetime: $this->datetime,
                request: $this->request,
                authContext: $this->authContext,
            )
            : throw new InvalidArgumentException(
                'Service class must implement ' . ServiceInterface::class
                . '[serviceClassName: ' . $serviceClassName . ']',
            );
    }
}
