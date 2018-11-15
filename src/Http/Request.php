<?php declare(strict_types = 1);

namespace Contributte\Fio\Http;

/**
 * ExportRequest
 */
class Request
{

	public const POST = 'POST';
	public const GET = 'GET';

	/** @var string */
	private $token;

	/** @var string */
	private $fileContents;

	/** @var string */
	private $url;

	/** @var string */
	private $requestType;

	public function __construct(string $url, string $token, string $requestType = self::GET)
	{
		$this->url = $url;
		$this->token = $token;
		$this->requestType = $requestType;
	}

	public function setToken(string $token): void
	{
		$this->token = $token;
	}

	public function getToken(): string
	{
		return $this->token;
	}

	public function getLang(): string
	{
		return 'en';
	}

	public function setUrl(string $url): void
	{
		$this->url = $url;
	}

	public function getUrl(): string
	{
		return $this->url;
	}

	public function getFileType(): ?string
	{
		if (!$this->hasFile()) {
			return null;
		}

		return 'xml';
	}

	public function setRequestType(string $requestType): void
	{
		$this->requestType = $requestType;
	}

	public function getRequestType(): string
	{
		return $this->requestType;
	}

	public function getFileContents(): string
	{
		return $this->fileContents;
	}

	public function setFileContents(string $fileContents): void
	{
		$this->fileContents = $fileContents;
	}

	public function hasFile(): bool
	{
		return isset($this->fileContents);
	}

}
