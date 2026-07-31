<?php declare(strict_types = 1);

namespace Contributte\Fio\Entity\Transaction;

use Contributte\Fio\Exceptions\InvalidPropertyException;

/**
 * ForeignTransaction (zahraniční platba mimo SEPA)
 */
final class ForeignTransaction extends AbroadTransaction
{

	public const NAME = 'ForeignTransaction';

	// Vše plátce (OUR)
	public const CHARGES_OUR = 470501;

	// Vše příjemce (BEN)
	public const CHARGES_BEN = 470502;

	// Každý sám své (SHA)
	public const CHARGES_SHA = 470503;

	private ?int $detailsOfCharges = null;

	private ?string $remittanceInfo4 = null;

	/**
	 * Recipient account number or IBAN, max 34 characters
	 */
	public function setAccountTo(string $accountTo): void
	{
		if (preg_match('/^[A-Za-z0-9]{1,34}$/', $accountTo) !== 1) {
			throw new InvalidPropertyException('$accountTo must be alphanumeric, max 34 characters.');
		}

		$this->accountTo = $accountTo;
	}

	/**
	 * Fees allocation, use one of the CHARGES_* constants
	 */
	public function setDetailsOfCharges(int $detailsOfCharges): void
	{
		$this->assertExactLength((string) $detailsOfCharges, 6, 'detailsOfCharges');

		$this->detailsOfCharges = $detailsOfCharges;
	}

	public function setRemittanceInfo4(string $remittanceInfo): void
	{
		$this->assertMaxLength($remittanceInfo, 35, 'remittanceInfo');

		$this->remittanceInfo4 = $remittanceInfo;
	}

	/**
	 * @return mixed[]
	 */
	public function toArray(): array
	{
		return array_merge(parent::toArray(), [
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
			'remittanceInfo4' => $this->remittanceInfo4,
			'detailsOfCharges' => $this->detailsOfCharges,
			'paymentReason' => $this->paymentReason,
		]);
	}

	public function isValid(): bool
	{
		return isset(
			$this->accountFrom,
			$this->amount,
			$this->accountTo,
			$this->bic,
			$this->date,
			$this->benefName,
			$this->benefStreet,
			$this->benefCity,
			$this->benefCountry,
			$this->remittanceInfo1,
			$this->detailsOfCharges,
			$this->paymentReason
		);
	}

}
