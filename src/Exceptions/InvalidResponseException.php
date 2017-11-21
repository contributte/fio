<?php declare(strict_types = 1);

namespace Markette\Fio\Exceptions;

use Throwable;

/**
 * InvalidResponseException
 *
 * @author Filip Suska <vody105@gmail.com>
 */
class InvalidResponseException extends RuntimeException
{

    /** @var string */
    protected $result;

    /**
     * @param string $message
     * @param int $code
     * @param Throwable|NULL $previous
     * @param string $result
     */
    public function __construct(string $message = '', int $code = 0, ?Throwable $previous = NULL, ?string $result = NULL)
    {
        parent::__construct($message, $code, $previous);

        $this->result = $result;
    }

    /**
     * @return string
     */
    public function getResult(): string
    {
        return $this->result;
    }

}
