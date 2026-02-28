<?php
declare(strict_types=1);

namespace App\Service\Controller\Shared\Process\Process\Fields;

/**
 *
 */
final class ProcessId implements \Stringable
{
    const LENGTH = 15;

    /**
     * 
     * @var string
     */
    private string $value;

    public function __construct(
        private readonly string $process_id
    ) {
        strlen($process_id) <= self::LENGTH || throw new \InvalidArgumentException(
                self::class . ' process_id length Error'
                . '[maxlength: ' . (string) self::LENGTH . ']'
                . '[process_id: ' . (string) $process_id . ']'
            );

        $this->value = substr(
            str_pad('', self::LENGTH, '0', STR_PAD_LEFT) . $this->process_id,
            self::LENGTH
        );
    }

    /**
     * 
     * @return string
     */
    public function toString() : string
    {
        return $this->value;
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