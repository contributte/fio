<?php declare(strict_types = 1);

namespace Contributte\Fio;

use Contributte\Fio\Http\IHttpClient;
use Contributte\Fio\Services\PaymentService;

/**
 * FioManager
 */
class FioManager
{

	/** @var Config */
	private $config;

	/** @var IHttpClient */
	private $httpClient;

	public function __construct(Config $config, IHttpClient $httpClient)
	{
		$this->config = $config;
		$this->httpClient = $httpClient;
	}

	public function createPaymentService(string $accountName): PaymentService
	{
		// We get account by name
		$account = $this->config->getAccountByName($accountName);

		return new PaymentService($account, $this->httpClient);
	}

}
