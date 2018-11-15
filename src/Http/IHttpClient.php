<?php declare(strict_types = 1);

namespace Contributte\Fio\Http;

/**
 * Interface IHttpClient
 */
interface IHttpClient
{

	public function sendRequest(Request $request): string;

}
