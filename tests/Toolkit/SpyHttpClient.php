<?php declare(strict_types = 1);

namespace Tests\Toolkit;

use Contributte\Fio\Http\IHttpClient;
use Contributte\Fio\Http\Request;

final class SpyHttpClient implements IHttpClient
{

	public ?string $requestedUrl = null;

	private string $response;

	public function __construct(string $response = '')
	{
		$this->response = $response;
	}

	public function sendRequest(Request $request): string
	{
		$this->requestedUrl = $request->getUrl();

		return $this->response;
	}

}
