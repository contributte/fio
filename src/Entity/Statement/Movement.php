<?php declare(strict_types = 1);

namespace Contributte\Fio\Entity\Statement;

use Contributte\Fio\Exceptions\InvalidResponseException;
use Contributte\Fio\Utils\XmlValue;
use DateTimeImmutable;
use SimpleXMLElement;

/**
 * Movement (single account movement from bank statement)
 */
class Movement
{

	private int $id;

	private DateTimeImmutable $date;

	private float $amount;

	private string $currency;

	private string $type;

	private ?string $counterAccount = null;

	private ?string $counterAccountName = null;

	private ?string $bankCode = null;

	private ?string $bankName = null;

	private ?string $ks = null;

	private ?string $vs = null;

	private ?string $ss = null;

	private ?string $userIdentification = null;

	private ?string $messageForRecipient = null;

	private ?string $performedBy = null;

	private ?string $specification = null;

	private ?string $comment = null;

	private ?string $bic = null;

	private ?int $instructionId = null;

	private ?string $payerReference = null;

	public function __construct(int $id, DateTimeImmutable $date, float $amount, string $currency, string $type)
	{
		$this->id = $id;
		$this->date = $date;
		$this->amount = $amount;
		$this->currency = $currency;
		$this->type = $type;
	}

	/**
	 * @throws InvalidResponseException when mandatory columns are missing
	 */
	public static function fromXml(SimpleXMLElement $transaction): self
	{
		$id = XmlValue::toIntOrNull($transaction, 'column_22');
		$date = XmlValue::toDateOrNull($transaction, 'column_0');
		$amount = XmlValue::toFloatOrNull($transaction, 'column_1');
		$currency = XmlValue::toStringOrNull($transaction, 'column_14');
		$type = XmlValue::toStringOrNull($transaction, 'column_8');

		if ($id === null || $date === null || $amount === null || $currency === null || $type === null) {
			throw new InvalidResponseException('Movement is missing mandatory columns.');
		}

		$movement = new self($id, $date, $amount, $currency, $type);
		$movement->counterAccount = XmlValue::toStringOrNull($transaction, 'column_2');
		$movement->counterAccountName = XmlValue::toStringOrNull($transaction, 'column_10');
		$movement->bankCode = XmlValue::toStringOrNull($transaction, 'column_3');
		$movement->bankName = XmlValue::toStringOrNull($transaction, 'column_12');
		$movement->ks = XmlValue::toStringOrNull($transaction, 'column_4');
		$movement->vs = XmlValue::toStringOrNull($transaction, 'column_5');
		$movement->ss = XmlValue::toStringOrNull($transaction, 'column_6');
		$movement->userIdentification = XmlValue::toStringOrNull($transaction, 'column_7');
		$movement->messageForRecipient = XmlValue::toStringOrNull($transaction, 'column_16');
		$movement->performedBy = XmlValue::toStringOrNull($transaction, 'column_9');
		$movement->specification = XmlValue::toStringOrNull($transaction, 'column_18');
		$movement->comment = XmlValue::toStringOrNull($transaction, 'column_25');
		$movement->bic = XmlValue::toStringOrNull($transaction, 'column_26');
		$movement->instructionId = XmlValue::toIntOrNull($transaction, 'column_17');
		$movement->payerReference = XmlValue::toStringOrNull($transaction, 'column_27');

		return $movement;
	}

	public function getId(): int
	{
		return $this->id;
	}

	public function getDate(): DateTimeImmutable
	{
		return $this->date;
	}

	public function getAmount(): float
	{
		return $this->amount;
	}

	public function getCurrency(): string
	{
		return $this->currency;
	}

	public function getType(): string
	{
		return $this->type;
	}

	public function getCounterAccount(): ?string
	{
		return $this->counterAccount;
	}

	public function getCounterAccountName(): ?string
	{
		return $this->counterAccountName;
	}

	public function getBankCode(): ?string
	{
		return $this->bankCode;
	}

	public function getBankName(): ?string
	{
		return $this->bankName;
	}

	public function getKs(): ?string
	{
		return $this->ks;
	}

	public function getVs(): ?string
	{
		return $this->vs;
	}

	public function getSs(): ?string
	{
		return $this->ss;
	}

	public function getUserIdentification(): ?string
	{
		return $this->userIdentification;
	}

	public function getMessageForRecipient(): ?string
	{
		return $this->messageForRecipient;
	}

	public function getPerformedBy(): ?string
	{
		return $this->performedBy;
	}

	public function getSpecification(): ?string
	{
		return $this->specification;
	}

	public function getComment(): ?string
	{
		return $this->comment;
	}

	public function getBic(): ?string
	{
		return $this->bic;
	}

	public function getInstructionId(): ?int
	{
		return $this->instructionId;
	}

	public function getPayerReference(): ?string
	{
		return $this->payerReference;
	}

}
