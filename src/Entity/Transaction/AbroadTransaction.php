<?php declare(strict_types = 1);

namespace Contributte\Fio\Entity\Transaction;

/**
 * AbroadTransaction (common fields of euro and foreign payments)
 */
abstract class AbroadTransaction extends Transaction
{

	protected string $benefName;

	protected ?string $bic = null;

	protected ?string $comment = null;

	protected ?string $benefStreet = null;

	protected ?string $benefCity = null;

	protected ?string $benefCountry = null;

	protected ?string $remittanceInfo1 = null;

	protected ?string $remittanceInfo2 = null;

	protected ?string $remittanceInfo3 = null;

	public function setBenefName(string $benefName): void
	{
		$this->assertMaxLength($benefName, 35, 'benefName');

		$this->benefName = $benefName;
	}

	/**
	 * ISO 9362, pad 8-char BIC with XXX
	 */
	public function setBic(string $bic): void
	{
		$this->assertExactLength($bic, 11, 'bic');

		$this->bic = $bic;
	}

	public function setComment(string $comment): void
	{
		$this->assertMaxLength($comment, 140, 'comment');

		$this->comment = $comment;
	}

	public function setBenefStreet(string $benefStreet): void
	{
		$this->assertMaxLength($benefStreet, 35, 'benefStreet');

		$this->benefStreet = $benefStreet;
	}

	public function setBenefCity(string $benefCity): void
	{
		$this->assertMaxLength($benefCity, 35, 'benefCity');

		$this->benefCity = $benefCity;
	}

	/**
	 * Country code of the account owner, see FIO API docs chapter 6.3.2
	 */
	public function setBenefCountry(string $benefCountry): void
	{
		$this->assertLengthBetween($benefCountry, 2, 3, 'benefCountry');

		$this->benefCountry = $benefCountry;
	}

	public function setRemittanceInfo1(string $remittanceInfo): void
	{
		$this->assertMaxLength($remittanceInfo, 35, 'remittanceInfo');

		$this->remittanceInfo1 = $remittanceInfo;
	}

	public function setRemittanceInfo2(string $remittanceInfo): void
	{
		$this->assertMaxLength($remittanceInfo, 35, 'remittanceInfo');

		$this->remittanceInfo2 = $remittanceInfo;
	}

	public function setRemittanceInfo3(string $remittanceInfo): void
	{
		$this->assertMaxLength($remittanceInfo, 35, 'remittanceInfo');

		$this->remittanceInfo3 = $remittanceInfo;
	}

}
