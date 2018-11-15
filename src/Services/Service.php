<?php declare(strict_types = 1);

namespace Contributte\Fio\Services;

use Contributte\Fio\Entity\Account\Account;
use Contributte\Fio\Http\IHttpClient;
use Contributte\Fio\Http\Request;

/**
 * Service
 */
abstract class Service
{

	/** @var IHttpClient */
	protected $httpClient;

	/** @var Account */
	protected $account;

	public function __construct(Account $account, IHttpClient $httpClient)
	{
		$this->httpClient = $httpClient;
		$this->account = $account;
	}

	abstract protected function createRequest(Request $request): string;

}
