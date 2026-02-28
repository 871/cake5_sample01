<?php declare(strict_types=1);

namespace App\Security\Auth\AuthContext\Fields;

class AccountId implements \Stringable
{
    public function __construct(
        private readonly ?int $value
    ) {
        $this->value === null
        || throw new \Exception(
            self::class . ' Generate Error'
            . '[value: ' . (string) $this->value . ']'
        );
    }

    /**
     * 
     * @return int
     */
    public function toInt() : ?int
    {
        return $this->value;
    }

    /**
     * 
     * @return string
     */
    public function toString() : string
    {
        return (string) $this->value;
    }

    /**
     * 
     * @return string
     */
    public function __toString() : string
    {
        return $this->toString();
    }
}