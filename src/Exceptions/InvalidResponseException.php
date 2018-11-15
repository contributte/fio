<?php declare(strict_types = 1);

namespace Contributte\Fio\Exceptions;

use Throwable;

/**
 * InvalidResponseException
 */
class InvalidResponseException extends RuntimeException
{

	/** @var string|null */
	protected $result;

	public function __construct(string $message = '', int $code = 0, ?Throwable $previous = null, ?string $result = null)
	{
		parent::__construct($message, $code, $previous);

		$this->result = $result;
	}

	public function getResult(): ?string
	{
		return $this->result;
	}

}
