<?php declare(strict_types = 1);

namespace Contributte\Fio;

use Contributte\Fio\Http\IHttpClient;
use Contributte\Fio\Services\PaymentService;

/**
 * FioManager
 *
 * @author Filip Suska <vody105@gmail.com>
 */
class FioManager
{

	/** @var Config */
	private $config;

	/** @var IHttpClient */
	private $httpClient;

	/**
	 * @param Config $config
	 * @param IHttpClient $httpClient
	 */
	public function __construct(Config $config, IHttpClient $httpClient)
	{
		$this->config = $config;
		$this->httpClient = $httpClient;
	}

	/**
	 * @param string $accountName
	 * @return PaymentService
	 */
	public function createPaymentService(string $accountName): PaymentService
	{
		// We get account by name
		$account = $this->config->getAccountByName($accountName);

		return new PaymentService($account, $this->httpClient);
	}

}
