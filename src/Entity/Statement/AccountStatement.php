<?php declare(strict_types = 1);

namespace Contributte\Fio\Entity\Statement;

use ArrayIterator;
use Contributte\Fio\Exceptions\InvalidResponseException;
use Contributte\Fio\Utils\XmlValue;
use DateTimeImmutable;
use IteratorAggregate;
use SimpleXMLElement;
use Throwable;

/**
 * AccountStatement (info + list of movements)
 *
 * @implements IteratorAggregate<int, Movement>
 */
class AccountStatement implements IteratorAggregate
{

	private string $accountId;

	private string $currency;

	private string $iban;

	private string $bic;

	private float $openingBalance;

	private float $closingBalance;

	private ?string $bankId = null;

	private ?DateTimeImmutable $dateStart = null;

	private ?DateTimeImmutable $dateEnd = null;

	private ?int $yearList = null;

	private ?int $idList = null;

	private ?int $idFrom = null;

	private ?int $idTo = null;

	private ?int $idLastDownload = null;

	/** @var Movement[] */
	private array $movements = [];

	public function __construct(string $accountId, string $currency, string $iban, string $bic, float $openingBalance, float $closingBalance)
	{
		$this->accountId = $accountId;
		$this->currency = $currency;
		$this->iban = $iban;
		$this->bic = $bic;
		$this->openingBalance = $openingBalance;
		$this->closingBalance = $closingBalance;
	}

	/**
	 * @throws InvalidResponseException when XML has unexpected structure
	 */
	public static function fromXml(string $xml): self
	{
		// Catch errors differently
		$prev = libxml_use_internal_errors(true);

		try {
			$sxe = new SimpleXMLElement($xml);
		} catch (Throwable $e) {
			throw new InvalidResponseException($e->getMessage(), (int) $e->getCode(), $e, $xml);
		} finally {
			libxml_clear_errors();
			libxml_use_internal_errors($prev);
		}

		if (!isset($sxe->Info)) {
			throw new InvalidResponseException('Unexpected XML structure.', 0, null, $xml);
		}

		$info = $sxe->Info;

		$accountId = XmlValue::toStringOrNull($info, 'accountId');
		$currency = XmlValue::toStringOrNull($info, 'currency');
		$iban = XmlValue::toStringOrNull($info, 'iban');
		$bic = XmlValue::toStringOrNull($info, 'bic');
		$openingBalance = XmlValue::toFloatOrNull($info, 'openingBalance');
		$closingBalance = XmlValue::toFloatOrNull($info, 'closingBalance');

		if ($accountId === null || $currency === null || $iban === null || $bic === null || $openingBalance === null || $closingBalance === null) {
			throw new InvalidResponseException('Unexpected XML structure.', 0, null, $xml);
		}

		$statement = new self($accountId, $currency, $iban, $bic, $openingBalance, $closingBalance);
		$statement->bankId = XmlValue::toStringOrNull($info, 'bankId');
		$statement->dateStart = XmlValue::toDateOrNull($info, 'dateStart');
		$statement->dateEnd = XmlValue::toDateOrNull($info, 'dateEnd');
		$statement->yearList = XmlValue::toIntOrNull($info, 'yearList');
		$statement->idList = XmlValue::toIntOrNull($info, 'idList');
		$statement->idFrom = XmlValue::toIntOrNull($info, 'idFrom');
		$statement->idTo = XmlValue::toIntOrNull($info, 'idTo');
		$statement->idLastDownload = XmlValue::toIntOrNull($info, 'idLastDownload');

		if (isset($sxe->TransactionList->Transaction)) {
			foreach ($sxe->TransactionList->Transaction as $transaction) {
				$statement->movements[] = Movement::fromXml($transaction);
			}
		}

		return $statement;
	}

	public function getAccountId(): string
	{
		return $this->accountId;
	}

	public function getCurrency(): string
	{
		return $this->currency;
	}

	public function getIban(): string
	{
		return $this->iban;
	}

	public function getBic(): string
	{
		return $this->bic;
	}

	public function getOpeningBalance(): float
	{
		return $this->openingBalance;
	}

	public function getClosingBalance(): float
	{
		return $this->closingBalance;
	}

	public function getBankId(): ?string
	{
		return $this->bankId;
	}

	public function getDateStart(): ?DateTimeImmutable
	{
		return $this->dateStart;
	}

	public function getDateEnd(): ?DateTimeImmutable
	{
		return $this->dateEnd;
	}

	public function getYearList(): ?int
	{
		return $this->yearList;
	}

	public function getIdList(): ?int
	{
		return $this->idList;
	}

	public function getIdFrom(): ?int
	{
		return $this->idFrom;
	}

	public function getIdTo(): ?int
	{
		return $this->idTo;
	}

	public function getIdLastDownload(): ?int
	{
		return $this->idLastDownload;
	}

	/**
	 * @return Movement[]
	 */
	public function getMovements(): array
	{
		return $this->movements;
	}

	/**
	 * @return ArrayIterator<int, Movement>
	 */
	public function getIterator(): ArrayIterator
	{
		return new ArrayIterator($this->movements);
	}

}
