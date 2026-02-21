<?php
declare(strict_types=1);

namespace App\Service\Controller;

interface ServiceInterface extends \App\Service\ServiceInterface
{
    /**
     * 
     * @param \Cake\Http\ServerRequest $request
     * @return self
     */
    public function setRequest(\Cake\Http\ServerRequest $request) : self;

    /**
     * 
     * @param \App\Security\Auth\AuthContext $authContext
     * @return self
     */
    public function setAuthContext(\App\Security\Auth\AuthContext $authContext) : self;

    /**
     * 
     * @param string $serviceClassName
     * @return \App\Service\Controller\ServiceInterface
     */
    public function createService(string $serviceClassName) : \App\Service\Controller\ServiceInterface;
}