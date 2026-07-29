<?php declare(strict_types = 1);

namespace Contributte\Fio\Services;

use Contributte\Fio\Entity\Statement\AccountStatement;
use Contributte\Fio\Exceptions\InvalidResponseException;
use Contributte\Fio\Exceptions\IOException;
use Contributte\Fio\Http\Request;
use DateTimeInterface;

/**
 * AccountService
 */
class AccountService extends Service
{

	private const URL = 'https://fioapi.fio.cz/v1/rest/';

	/**
	 * Downloads movements for given period
	 *
	 * @throws InvalidResponseException|IOException
	 */
	public function movementsForPeriod(DateTimeInterface $from, DateTimeInterface $to): AccountStatement
	{
		$url = sprintf(
			'%speriods/%s/%s/%s/transactions.xml',
			self::URL,
			$this->account->getToken(),
			$from->format('Y-m-d'),
			$to->format('Y-m-d')
		);

		return AccountStatement::fromXml($this->createRequest(new Request($url, $this->account->getToken())));
	}

	/**
	 * Downloads movements since the last download (moves the server-side cursor)
	 *
	 * @throws InvalidResponseException|IOException
	 */
	public function movementsFromLastRequest(): AccountStatement
	{
		$url = sprintf(
			'%slast/%s/transactions.xml',
			self::URL,
			$this->account->getToken()
		);

		return AccountStatement::fromXml($this->createRequest(new Request($url, $this->account->getToken())));
	}

	/**
	 * Downloads official statement by year and number
	 *
	 * @throws InvalidResponseException|IOException
	 */
	public function movementsForStatement(int $year, int $id): AccountStatement
	{
		$url = sprintf(
			'%sby-id/%s/%d/%d/transactions.xml',
			self::URL,
			$this->account->getToken(),
			$year,
			$id
		);

		return AccountStatement::fromXml($this->createRequest(new Request($url, $this->account->getToken())));
	}

	/**
	 * Sets the cursor to the ID of the last successfully downloaded movement
	 *
	 * @throws IOException
	 */
	public function setLastId(int $id): void
	{
		$url = sprintf(
			'%sset-last-id/%s/%d/',
			self::URL,
			$this->account->getToken(),
			$id
		);

		$this->createRequest(new Request($url, $this->account->getToken()));
	}

	/**
	 * Sets the cursor to the date of the last unsuccessfully downloaded day
	 *
	 * @throws IOException
	 */
	public function setLastDate(DateTimeInterface $date): void
	{
		$url = sprintf(
			'%sset-last-date/%s/%s/',
			self::URL,
			$this->account->getToken(),
			$date->format('Y-m-d')
		);

		$this->createRequest(new Request($url, $this->account->getToken()));
	}

	protected function createRequest(Request $request): string
	{
		// Ask HttpClient to execute request
		return $this->httpClient->sendRequest($request);
	}

}
