<?php declare(strict_types = 1);

namespace Contributte\Fio\Entity\Transaction;

use Contributte\Fio\Exceptions\InvalidPropertyException;
use DateTimeInterface;

/**
 * Transaction
 *
 * @author Filip Suska <vody105@gmail.com>
 */
abstract class Transaction
{

	public const NAME = 'Abstract Transaction';

	// Currency
	public const CZK = 'CZK';
	public const EURO = 'EUR';

	/** @var string */
	protected $accountFrom;

	/** @var string */
	protected $currency = self::CZK;

	/** @var float */
	protected $amount;

	/** @var string */
	protected $accountTo;

	/** @var string */
	protected $date;

	/**
	 * @param string $accountFrom
	 * @return void
	 */
	public function setAccountFrom(string $accountFrom): void
	{
		if (strlen($accountFrom) > 16) {
			throw new InvalidPropertyException('Maximum accountFrom length is 16.');
		}

		$this->accountFrom = $accountFrom;
	}

	/**
	 * @param string $currency
	 * @return void
	 */
	public function setCurrency(string $currency): void
	{
		if (strlen($currency) !== 3) {
			throw new InvalidPropertyException('Currency code length must be 3.');
		}

		$this->currency = $currency;
	}

	/**
	 * @param float $amount
	 * @return void
	 */
	public function setAmount(float $amount): void
	{
		if (strlen((string) $amount) > 18) {
			throw new InvalidPropertyException('Maximum amount length is 18.');
		}

		$this->amount = $amount;
	}

	/**
	 * @param string $accountTo
	 * @return void
	 */
	public function setAccountTo(string $accountTo): void
	{
		if (!preg_match('/^(\d{1,6}-)?\d{0,10}$/', $accountTo)) {
			throw new InvalidPropertyException('$accountTo format must be string: prefix{max 6digits}-number{max 10 digits}');
		}

		$this->accountTo = $accountTo;
	}

	/**
	 * @param DateTimeInterface $date
	 * @return void
	 */
	public function setDate(DateTimeInterface $date): void
	{
		$this->date = $date->format('Y-m-d');
	}

	/**
	 * @return mixed[]
	 */
	public function toArray(): array
	{
		return [
			'accountFrom' => $this->accountFrom,
			'currency' => $this->currency,
			'amount' => $this->amount,
			'accountTo' => $this->accountTo,
		];
	}

	/**
	 * Checks if all mandatory data are set
	 *
	 * @return bool
	 */
	abstract public function isValid(): bool;

}
