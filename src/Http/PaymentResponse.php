<?php declare(strict_types = 1);

namespace Contributte\Fio\Http;

use Contributte\Fio\Exceptions\InvalidResponseException;
use LibXMLError;
use SimpleXMLElement;
use Throwable;
use Traversable;

/**
 * ExportResponse
 */
class PaymentResponse
{

	private const SUCCESS = 'ok';

	private const ERR_MSG = [
		0 => 'ok - transaction accepted',
		1 => 'errors found in transactions',
		2 => 'warning - some values are invalid',
		11 => 'syntactic error',
		12 => 'empty import - no transactions found',
		13 => 'file is too large - file cannot be larger than 2 MB',
		14 => 'empty file - no transactions in file',
	];

	/** @var SimpleXMLElement */
	private $xml;

	/** @var string */
	private $pureResult;

	/**
	 * @throws InvalidResponseException when unexpected XML structure
	 */
	public function __construct(string $result)
	{
		// Save pure result
		$this->pureResult = $result;

		// Catch errors differently
		$prev = libxml_use_internal_errors(true);

		try {
			$this->xml = new SimpleXMLElement($result);
		} catch (Throwable $e) {
			throw new InvalidResponseException($e->getMessage(), $e->getCode(), $e, $result);
		}

		/** @var LibXMLError $e */
		foreach (libxml_get_errors() as $e) {
			throw new InvalidResponseException($e->message, $e->code, $e, $result);
		}

		// Reset catching errors to previouse
		libxml_use_internal_errors($prev);
	}

	public function isOk(): bool
	{
		return $this->getStatus() === self::SUCCESS;
	}

	/**
	 * @throws InvalidResponseException when unexpected XML structure
	 */
	public function getStatus(): string
	{
		if (!isset($this->getResult()->status)) {
			throw new InvalidResponseException('Unexpected XML structure.', 0, null, $this->pureResult);
		}

		return (string) $this->getResult()->status;
	}

	/**
	 * @throws InvalidResponseException when unexpected XML structure
	 */
	public function getErrorCode(): int
	{
		if (!isset($this->getResult()->errorCode)) {
			throw new InvalidResponseException('Unexpected XML structure.', 0, null, $this->pureResult);
		}

		return (int) $this->getResult()->errorCode;
	}

	public function getErrorCodeMessage(): ?string
	{
		return self::ERR_MSG[$this->getErrorCode()] ?? null;
	}

	/**
	 * @return SimpleXMLElement[]|Traversable
	 * @throws InvalidResponseException when unexpected XML structure
	 */
	private function getResult(): Traversable
	{
		if (!isset($this->xml->result)) {
			throw new InvalidResponseException('Unexpected XML structure.', 0, null, $this->pureResult);
		}

		return $this->xml->result;
	}

}
