<?php declare(strict_types = 1);

namespace Contributte\Fio\Entity\Transaction;

use Contributte\Fio\Exceptions\InvalidPropertyException;

/**
 * DomesticTransaction
 *
 * @author Filip Suska <vody105@gmail.com>
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

	/**
	 * @param int $paymentType
	 * @return void
	 */
	public function setPaymentType(int $paymentType): void
	{
		if (strlen((string) $paymentType) !== 6) {
			throw new InvalidPropertyException('$paymentType has to be 6 digits long.');
		}

		$this->paymentType = $paymentType;
	}

	/**
	 * @param string $bankCode
	 * @return void
	 */
	public function setBankCode(string $bankCode): void
	{
		if (strlen((string) $bankCode) !== 4) {
			throw new InvalidPropertyException('$bankCode must be 4 digits long.');
		}

		$this->bankCode = $bankCode;
	}

	/**
	 * @param string $ks
	 * @return void
	 */
	public function setKs(string $ks): void
	{
		if (strlen($ks) > 4) {
			throw new InvalidPropertyException('$ks can be maximum of 4 digits long.');
		}
		$this->ks = $ks;
	}

	/**
	 * @param string $vs
	 * @return void
	 */
	public function setVs(string $vs): void
	{
		if (strlen($vs) > 10) {
			throw new InvalidPropertyException('$vs can be maximum of 10 digits long.');
		}

		$this->vs = $vs;
	}

	/**
	 * @param string $ss
	 * @return void
	 */
	public function setSs(string $ss): void
	{
		if (strlen($ss) > 10) {
			throw new InvalidPropertyException('$ss can be maximum of 10 digits long.');
		}

		$this->ss = $ss;
	}

	/**
	 * @param string $messageForRecipient
	 * @return void
	 */
	public function setMessageForRecipient(string $messageForRecipient): void
	{
		if (strlen($messageForRecipient) > 140) {
			throw new InvalidPropertyException('$messageForRecipient can be maximum of 140 characters long.');
		}

		$this->messageForRecipient = $messageForRecipient;
	}

	/**
	 * @param string $comment
	 * @return void
	 */
	public function setComment(string $comment): void
	{
		if (strlen($comment) > 255) {
			throw new InvalidPropertyException('$comment can be maximum of 255 characters long.');
		}

		$this->comment = $comment;
	}

	/**
	 * @param int $paymentReason
	 * @return void
	 */
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

	/**
	 * @return bool
	 */
	public function isValid(): bool
	{
		if (isset(
			$this->accountFrom,
			$this->currency,
			$this->amount,
			$this->accountTo,
			$this->bankCode,
			$this->date
		)) {
			return TRUE;
		}

		return FALSE;
	}

}
