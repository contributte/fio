<?php declare(strict_types = 1);

namespace Contributte\Fio\Entity\Transaction;

/**
 * DomesticTransaction
 */
final class DomesticTransaction extends Transaction
{

	public const NAME = 'DomesticTransaction';

	// Standardní
	public const PAYMENT_TYPE_STANDARD = 431001;

	// Zrychlená
	public const PAYMENT_TYPE_FASTER = 431004;

	// Prioritní
	public const PAYMENT_TYPE_PRIORITY = 431005;

	// Příkaz k inkasu
	public const PAYMENT_TYPE_COLLECTION = 431022;

	private string $bankCode;

	private ?string $ks = null;

	private ?string $vs = null;

	private ?string $ss = null;

	private ?string $messageForRecipient = null;

	private ?string $comment = null;

	public function setBankCode(string $bankCode): void
	{
		$this->assertExactLength($bankCode, 4, 'bankCode');

		$this->bankCode = $bankCode;
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

	public function setMessageForRecipient(string $messageForRecipient): void
	{
		$this->assertMaxLength($messageForRecipient, 140, 'messageForRecipient');

		$this->messageForRecipient = $messageForRecipient;
	}

	public function setComment(string $comment): void
	{
		$this->assertMaxLength($comment, 255, 'comment');

		$this->comment = $comment;
	}

	/**
	 * @return mixed[]
	 */
	public function toArray(): array
	{
		return array_merge(parent::toArray(), [
			'bankCode' => $this->bankCode,
			'ks' => $this->ks,
			'vs' => $this->vs,
			'ss' => $this->ss,
			'date' => $this->date,
			'messageForRecipient' => $this->messageForRecipient,
			'comment' => $this->comment,
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
			$this->bankCode,
			$this->date
		);
	}

}
