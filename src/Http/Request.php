<?php declare(strict_types = 1);

namespace Contributte\Fio\Http;

/**
 * ExportRequest
 *
 * @author Filip Suska <vody105@gmail.com>
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

	/**
	 * @param string $url
	 * @param string $token
	 * @param string $requestType
	 */
	public function __construct(string $url, string $token, string $requestType = self::GET)
	{
		$this->url = $url;
		$this->token = $token;
		$this->requestType = $requestType;
	}

	/**
	 * @param string $token
	 * @return void
	 */
	public function setToken(string $token): void
	{
		$this->token = $token;
	}

	/**
	 * @return string
	 */
	public function getToken(): string
	{
		return $this->token;
	}

	/**
	 * @return string
	 */
	public function getLang(): string
	{
		return 'en';
	}

	/**
	 * @param string $url
	 * @return void
	 */
	public function setUrl(string $url): void
	{
		$this->url = $url;
	}

	/**
	 * @return string
	 */
	public function getUrl(): string
	{
		return $this->url;
	}

	/**
	 * @return string|NULL
	 */
	public function getFileType(): ?string
	{
		if (!$this->hasFile()) {
			return NULL;
		}

		return 'xml';
	}

	/**
	 * @param string $requestType
	 * @return void
	 */
	public function setRequestType(string $requestType): void
	{
		$this->requestType = $requestType;
	}

	/**
	 * @return string
	 */
	public function getRequestType(): string
	{
		return $this->requestType;
	}

	/**
	 * @return string
	 */
	public function getFileContents(): string
	{
		return $this->fileContents;
	}

	/**
	 * @param string $fileContents
	 * @return void
	 */
	public function setFileContents(string $fileContents): void
	{
		$this->fileContents = $fileContents;
	}

	/**
	 * @return bool
	 */
	public function hasFile(): bool
	{
		return isset($this->fileContents);
	}

}
