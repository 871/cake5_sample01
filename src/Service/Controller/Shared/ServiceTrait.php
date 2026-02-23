<?php
declare(strict_types=1);

namespace App\Service\Controller\Shared;

trait ServiceTrait
{
    /**
     * 
     * @var \DateTimeInterface
     */
    private \DateTimeInterface $datetime;

    /**
     * 
     * @var \Cake\Http\ServerRequest
     */
    private \Cake\Http\ServerRequest $request;

    /**
     * 
     * @var \App\Security\Auth\AuthContext
     */
    private \App\Security\Auth\AuthContext $authContext;

    /**
     * 
     * @var \Cake\Controller\Controller
     */
    private \Cake\Controller\Controller $controller;

    public function __construct(
        private ServiceParamsInterface $params
    ) {
        $this->datetime = $params->getDatetime();
        $this->request = $params->getRequest();
        $this->authContext = $params->getAuthContext();
        $this->controller = $params->getController();
    }

    /**
     * 
     * @param string $serviceClassName
     * @return ServiceInterface
     */
    public function createService(string $serviceClassName) : ServiceInterface
    {
        return is_subclass_of($serviceClassName, ServiceInterface::class)
            ? new $serviceClassName($this->params)
            : throw new \InvalidArgumentException(
                'Service class must implement ' . ServiceInterface::class
                . '[serviceClassName: ' . $serviceClassName . ']'
            );
    }
}