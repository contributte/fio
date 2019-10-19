<?php declare(strict_types = 1);

namespace Contributte\Fio\Entity\Transaction;

use Contributte\Fio\Exceptions\InvalidPropertyException;

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

	/** @var int */
	private $paymentType;

	/** @var string */
	private $bankCode;

	/** @var string */
	private $ks;

	/** @var string */
	private $vs;

	/** @var string */
	private $ss;

	/** @var string */
	private $messageForRecipient;

	/** @var string */
	private $comment;

	/** @var int */
	private $paymentReason;

	public function setPaymentType(int $paymentType): void
	{
		if (strlen((string) $paymentType) !== 6) {
			throw new InvalidPropertyException('$paymentType has to be 6 digits long.');
		}

		$this->paymentType = $paymentType;
	}

	public function setBankCode(string $bankCode): void
	{
		if (strlen($bankCode) !== 4) {
			throw new InvalidPropertyException('$bankCode must be 4 digits long.');
		}

		$this->bankCode = $bankCode;
	}

	public function setKs(string $ks): void
	{
		if (strlen($ks) > 4) {
			throw new InvalidPropertyException('$ks can be maximum of 4 digits long.');
		}

		$this->ks = $ks;
	}

	public function setVs(string $vs): void
	{
		if (strlen($vs) > 10) {
			throw new InvalidPropertyException('$vs can be maximum of 10 digits long.');
		}

		$this->vs = $vs;
	}

	public function setSs(string $ss): void
	{
		if (strlen($ss) > 10) {
			throw new InvalidPropertyException('$ss can be maximum of 10 digits long.');
		}

		$this->ss = $ss;
	}

	public function setMessageForRecipient(string $messageForRecipient): void
	{
		if (strlen($messageForRecipient) > 140) {
			throw new InvalidPropertyException('$messageForRecipient can be maximum of 140 characters long.');
		}

		$this->messageForRecipient = $messageForRecipient;
	}

	public function setComment(string $comment): void
	{
		if (strlen($comment) > 255) {
			throw new InvalidPropertyException('$comment can be maximum of 255 characters long.');
		}

		$this->comment = $comment;
	}

	public function setPaymentReason(int $paymentReason): void
	{
		if (strlen((string) $paymentReason) !== 3) {
			throw new InvalidPropertyException('$paymentReason must be 3 digits long.');
		}

		$this->paymentReason = $paymentReason;
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
			$this->currency,
			$this->amount,
			$this->accountTo,
			$this->bankCode,
			$this->date
		);
	}

}
