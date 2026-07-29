<?php declare(strict_types = 1);

namespace Contributte\Fio\Exceptions;

/**
 * HttpStatusException
 */
class HttpStatusException extends RuntimeException
{

	// Hints taken from FIO API documentation, chapter 8 "Známé chybové stavy"
	private const STATUS_HINTS = [
		404 => 'Check URL parameters of the request.',
		409 => 'Minimum interval of 30 seconds between requests with the same token was not respected.',
		413 => 'Too many movements requested (limit is 50 000). Narrow the date range or move the cursor to a newer movement.',
		422 => 'Data older than 90 days requested without unlocking full history in the internetbanking (Settings - API).',
		500 => 'Token does not exist or is inactive. Check the token in the internetbanking.',
	];

	protected ?string $result = null;

	public static function fromStatusCode(int $statusCode, ?string $result = null): self
	{
		$message = sprintf('Server returned HTTP status %d.', $statusCode);

		if (isset(self::STATUS_HINTS[$statusCode])) {
			$message .= ' ' . self::STATUS_HINTS[$statusCode];
		}

		$exception = new self($message, $statusCode);
		$exception->result = $result;

		return $exception;
	}

	public function getStatusCode(): int
	{
		return $this->getCode();
	}

	public function getResult(): ?string
	{
		return $this->result;
	}

}
