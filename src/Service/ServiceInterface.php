<?php
declare(strict_types=1);

namespace App\Service;

interface ServiceInterface
{
    /**
     * 
     * @return self
     */
    public static function getInstance() : self;

    /**
    * @param \DateTimeImmutable $datetime
    * @return self
    */
    public function setDatetime(\DateTimeImmutable $datetime) : self;    
}