<?php
declare(strict_types=1);

namespace App\Controller;

trait ServiceParamsTrait
{
    /**
     * 
     * @return \DateTimeInterface
     */
    public function getDatetime() : \DateTimeInterface
    {
        static $datetime = new \DateTimeImmutable();

        return $datetime;
    }

    /**
     * 
     * @return \Cake\Http\ServerRequest
     */
    public function getRequest(): \Cake\Http\ServerRequest 
    {
        return $this->request;
    }

    /**
     * 
     * @return \App\Security\Auth\AuthContext
     */
    public function getAuthContext() : \App\Security\Auth\AuthContext
    {
        return \App\Security\Auth\AuthContextResolver::getInstance($this->request)
            ->resolve();
    }

    /**
     * 
     * @return \Cake\Controller\Controller
     */
    public function getController() : \Cake\Controller\Controller
    {
        return $this;
    }
}