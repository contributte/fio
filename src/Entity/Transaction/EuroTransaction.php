<?php declare(strict_types = 1);

namespace Contributte\Fio\Entity\Transaction;

use Contributte\Fio\Exceptions\InvalidPropertyException;

/**
 * EuroTransaction (Europlatba, XML element T2Transaction)
 */
final class EuroTransaction extends AbroadTransaction
{

	public const NAME = 'T2Transaction';

	// Standardní
	public const PAYMENT_TYPE_STANDARD = 431008;

	// Prioritní
	public const PAYMENT_TYPE_PRIORITY = 431009;

	// Okamžitá
	public const PAYMENT_TYPE_INSTANT = 431018;

	protected string $currency = self::EURO;

	private ?string $ks = null;

	private ?string $vs = null;

	private ?string $ss = null;

	/**
	 * Recipient account is an IBAN (ISO 13616)
	 */
	public function setAccountTo(string $accountTo): void
	{
		if (preg_match('/^[A-Za-z]{2}\d{2}[A-Za-z0-9]{1,30}$/', $accountTo) !== 1) {
			throw new InvalidPropertyException('$accountTo must be a valid IBAN (max 34 characters).');
		}

		$this->accountTo = $accountTo;
	}

	public function setKs(string $ks): void
	{
		$this->assertMaxLength($ks, 4, 'ks');

		$this->ks = $ks;
	}

	public function setVs(string $vs): void
	{
		$this->assertMaxLength($vs, 10, 'vs');

		$this->vs = $vs;
	}

	public function setSs(string $ss): void
	{
		$this->assertMaxLength($ss, 10, 'ss');

		$this->ss = $ss;
	}

	/**
	 * @return mixed[]
	 */
	public function toArray(): array
	{
		return array_merge(parent::toArray(), [
			'ks' => $this->ks,
			'vs' => $this->vs,
			'ss' => $this->ss,
			'bic' => $this->bic,
			'date' => $this->date,
			'comment' => $this->comment,
			'benefName' => $this->benefName,
			'benefStreet' => $this->benefStreet,
			'benefCity' => $this->benefCity,
			'benefCountry' => $this->benefCountry,
			'remittanceInfo1' => $this->remittanceInfo1,
			'remittanceInfo2' => $this->remittanceInfo2,
			'remittanceInfo3' => $this->remittanceInfo3,
			'paymentReason' => $this->paymentReason,
			'paymentType' => $this->paymentType,
		]);
	}

	public function isValid(): bool
	{
		return isset(
			$this->accountFrom,
			$this->amount,
			$this->accountTo,
			$this->date,
			$this->benefName
		);
	}

}
