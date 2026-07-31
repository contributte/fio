<?php declare(strict_types = 1);

namespace Contributte\Fio\Entity\Transaction;

use Contributte\Fio\Exceptions\InvalidPropertyException;
use DateTimeInterface;

/**
 * Transaction
 */
abstract class Transaction
{

	public const NAME = 'Abstract Transaction';

	// Currency
	public const CZK = 'CZK';
	public const EURO = 'EUR';

	protected string $accountFrom;

	protected string $currency = self::CZK;

	protected float $amount;

	protected string $accountTo;

	protected string $date;

	protected ?int $paymentReason = null;

	protected ?int $paymentType = null;

	/**
	 * Checks if all mandatory data are set
	 */
	abstract public function isValid(): bool;

	public function setAccountFrom(string $accountFrom): void
	{
		$this->assertMaxLength($accountFrom, 16, 'accountFrom');

		$this->accountFrom = $accountFrom;
	}

	public function setCurrency(string $currency): void
	{
		$this->assertExactLength($currency, 3, 'currency');

		$this->currency = $currency;
	}

	public function setAmount(float $amount): void
	{
		if (strlen((string) $amount) > 18) {
			throw new InvalidPropertyException('Maximum amount length is 18.');
		}

		$this->amount = $amount;
	}

	public function setAccountTo(string $accountTo): void
	{
		if (preg_match('/^(\d{1,6}-)?\d{0,10}$/', $accountTo) !== 1) {
			throw new InvalidPropertyException('$accountTo format must be string: prefix{max 6digits}-number{max 10 digits}');
		}

		$this->accountTo = $accountTo;
	}

	public function setDate(DateTimeInterface $date): void
	{
		$this->date = $date->format('Y-m-d');
	}

	/**
	 * Payment reason code, see FIO API docs chapter 6.3.4
	 */
	public function setPaymentReason(int $paymentReason): void
	{
		$this->assertExactLength((string) $paymentReason, 3, 'paymentReason');

		$this->paymentReason = $paymentReason;
	}

	/**
	 * Allowed values are the PAYMENT_TYPE_* constants of the concrete transaction
	 */
	public function setPaymentType(int $paymentType): void
	{
		$this->assertExactLength((string) $paymentType, 6, 'paymentType');

		$this->paymentType = $paymentType;
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
	 * @throws InvalidPropertyException
	 */
	protected function assertMaxLength(string $value, int $maxLength, string $property): void
	{
		if (strlen($value) > $maxLength) {
			throw new InvalidPropertyException(sprintf('$%s can be maximum of %d characters long.', $property, $maxLength));
		}
	}

	/**
	 * @throws InvalidPropertyException
	 */
	protected function assertExactLength(string $value, int $length, string $property): void
	{
		if (strlen($value) !== $length) {
			throw new InvalidPropertyException(sprintf('$%s must be exactly %d characters long.', $property, $length));
		}
	}

	/**
	 * @throws InvalidPropertyException
	 */
	protected function assertLengthBetween(string $value, int $minLength, int $maxLength, string $property): void
	{
		if (strlen($value) < $minLength || strlen($value) > $maxLength) {
			throw new InvalidPropertyException(sprintf('$%s must be between %d and %d characters long.', $property, $minLength, $maxLength));
		}
	}

}
