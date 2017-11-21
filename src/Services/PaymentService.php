<?php declare(strict_types = 1);

namespace Markette\Fio\Services;

use Markette\Fio\Entity\Account\Account;
use Markette\Fio\Entity\Transaction\Transaction;
use Markette\Fio\Entity\Transaction\TransactionList;
use Markette\Fio\Exceptions\InvalidResponseException;
use Markette\Fio\Exceptions\InvalidTransactionException;
use Markette\Fio\Exceptions\IOException;
use Markette\Fio\Http\IHttpClient;
use Markette\Fio\Http\PaymentResponse;
use Markette\Fio\Http\Request;

/**
 * ExportClient
 *
 * @author Filip Suska <vody105@gmail.com>
 */
class PaymentService extends Service
{

    private const URL = 'https://www.fio.cz/ib_api/rest/import/';

    /** @var TransactionList */
    private $transactionList;

    /**
     * @param Account $account
     * @param IHttpClient $httpClient
     */
    public function __construct(Account $account, IHttpClient $httpClient)
    {
        parent::__construct($account, $httpClient);
    }

    /**
     * @return TransactionList
     */
    public function getTransactionList(): TransactionList
    {
        if (!$this->transactionList) {
            $this->transactionList = new TransactionList();
        }

        return $this->transactionList;
    }

    /**
     * @param Transaction $transaction
     * @return void
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
     * @return PaymentResponse
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
        $response = new PaymentResponse($result);

        return $response;
    }

    /**
     * @param Request $request
     * @return string
     */
    protected function createRequest(Request $request): string
    {
        // Ask HttpClient to execute request
        return $this->httpClient->sendRequest($request);
    }

}
