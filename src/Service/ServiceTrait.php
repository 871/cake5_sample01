<?php
declare(strict_types=1);

namespace App\Service;

trait ServiceTrait
{
    /**
    * @var \DateTimeImmutable
    */ 
    protected \DateTimeImmutable $datetime;

    /**
     * 
     * @return self
     */
    public static function getInstance() : self
    {
        static $ins = null;
        if ($ins === null) {
            $ins = new self();
        }

        return $ins;
    }

    /**
    * @param \DateTimeImmutable $datetime
    * @return self
    */
    public function setDatetime(\DateTimeImmutable $datetime) : self
    {
        $this->datetime = $datetime;

        return $this;
    }
}