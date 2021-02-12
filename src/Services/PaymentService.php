<?php declare(strict_types = 1);

namespace Contributte\Fio\Services;

use Contributte\Fio\Entity\Account\Account;
use Contributte\Fio\Entity\Transaction\Transaction;
use Contributte\Fio\Entity\Transaction\TransactionList;
use Contributte\Fio\Exceptions\InvalidResponseException;
use Contributte\Fio\Exceptions\InvalidTransactionException;
use Contributte\Fio\Exceptions\IOException;
use Contributte\Fio\Http\IHttpClient;
use Contributte\Fio\Http\PaymentResponse;
use Contributte\Fio\Http\Request;

/**
 * ExportClient
 */
class PaymentService extends Service
{

	private const URL = 'https://www.fio.cz/ib_api/rest/import/';

	/** @var TransactionList|null */
	private $transactionList;

	public function __construct(Account $account, IHttpClient $httpClient)
	{
		parent::__construct($account, $httpClient);
	}

	public function getTransactionList(): TransactionList
	{
		if ($this->transactionList === null) {
			$this->transactionList = new TransactionList();
		}

		return $this->transactionList;
	}

	/**
	 * @throws InvalidTransactionException
	 */
	public function addPayment(Transaction $transaction): void
	{
		// Add AccountFrom to all transactions from config
		$transaction->setAccountFrom($this->account->getAccountNum());

		// Check if transaction has all mandatory data
		if (!$transaction->isValid()) {
			throw new InvalidTransactionException('All transactions must contain mandatory data.');
		}

		$this->getTransactionList()->addTransaction($transaction);
	}

	/**
	 * @throws InvalidResponseException|IOException
	 */
	public function sendPayments(): PaymentResponse
	{
		// Generate XML from TransactionList
		$xml = $this->getTransactionList()->toXml();

		// Create request
		$request = new Request(self::URL, $this->account->getToken(), Request::POST);
		$request->setFileContents($xml);

		// Send created request
		$result = $this->createRequest($request);

		// Manage response
		return new PaymentResponse($result);
	}

	protected function createRequest(Request $request): string
	{
		// Ask HttpClient to execute request
		return $this->httpClient->sendRequest($request);
	}

}
