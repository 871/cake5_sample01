<?php
declare(strict_types=1);

namespace App\Service\Controller\Shared;

trait ServiceTrait
{
    public function __construct(
        private readonly \DateTimeInterface $datetime,
        private readonly \Cake\Http\ServerRequest $request,
        private readonly \App\Security\Auth\AuthContext $authContext,
    ) {
        // 処理なし
    }

    /**
     * 
     * @param string $serviceClassName
     * @return ServiceInterface
     */
    public function createService(string $serviceClassName) : ServiceInterface
    {
        return is_subclass_of($serviceClassName, ServiceInterface::class)
            ? new $serviceClassName(
                    datetime: $this->datetime,
                    request: $this->request,
                    authContext: $this->authContext
                )
            : throw new \InvalidArgumentException(
                'Service class must implement ' . ServiceInterface::class
                . '[serviceClassName: ' . $serviceClassName . ']'
            );
    }
}