<?php
declare(strict_types=1);

namespace App\Service\Controller;

trait ServiceTrait
{
    use \App\Service\ServiceTrait;

    /**
     * 
     * @var \Cake\Http\ServerRequest
     */
    protected \Cake\Http\ServerRequest $request;

    /** 
     * 
     * @var \App\Security\Auth\AuthContext
     */
    protected \App\Security\Auth\AuthContext $authContext;

    /**
     * 
     * @param \Cake\Http\ServerRequest $request
     * @return self
     */
    public function setRequest(\Cake\Http\ServerRequest $request) : self
    {
        $this->request = $request;

        return $this;
    }

    /**
     * 
     * @param \App\Security\Auth\AuthContext $authContext
     * @return self
     */
    public function setAuthContext(\App\Security\Auth\AuthContext $authContext) : self
    {
        $this->authContext = $authContext;

        return $this;
    }

    /**
     * 
     * @param string $serviceClassName
     * @return \App\Service\Controller\ServiceInterface
     */
    public function createService(string $serviceClassName) : \App\Service\Controller\ServiceInterface
    {
        if (!$serviceClassName instanceof \App\Service\Controller\ServiceInterface) {
            
            throw new \InvalidArgumentException(
                'Service class must implement \App\Service\Controller\ServiceInterface'
                . '[serviceClassName: ' . $serviceClassName . ']'
            );
        }

        return (function() use ($serviceClassName) : \App\Service\Controller\ServiceInterface {

            return $serviceClassName::getInstance()
                ->setDatetime($this->datetime);
        })()
            ->setRequest($this->request)
            ->setAuthContext($this->authContext);
    }
}