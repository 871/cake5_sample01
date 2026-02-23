<?php
declare(strict_types=1);

namespace App\Service\Controller\Shared;

interface ServiceParamsInterface
{
    /**
     * 
     * @return \DateTimeInterface
     */
    public function getDatetime() : \DateTimeInterface;

    /**
     * 
     * @return \Cake\Http\ServerRequest
     */
    public function getRequest(): \Cake\Http\ServerRequest;

    /**
     * 
     * @return \App\Security\Auth\AuthContext
     */
    public function getAuthContext() : \App\Security\Auth\AuthContext;

    /**
     * 
     * @return \Cake\Controller\Controller
     */
    public function getController() : \Cake\Controller\Controller;
}