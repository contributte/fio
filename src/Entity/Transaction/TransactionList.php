<?php declare(strict_types = 1);

namespace Contributte\Fio\Entity\Transaction;

use ArrayIterator;
use Contributte\Fio\Utils\ExportXmlGenerator;
use IteratorAggregate;

/**
 * @implements IteratorAggregate<Transaction>
 */
class TransactionList implements IteratorAggregate
{

	// Bank rejects the import file unless types keep this order (FIO API docs 6.3).
	// Keyed by XML element name so custom Transaction subclasses sort correctly too.
	private const EXPORT_ORDER = [
		DomesticTransaction::NAME => 0,
		EuroTransaction::NAME => 1,
		ForeignTransaction::NAME => 2,
	];

	/** @var Transaction[] */
	protected array $transactions = [];

	public function addTransaction(Transaction $transaction): void
	{
		$this->transactions[] = $transaction;
	}

	/**
	 * @return ArrayIterator<int, Transaction>
	 */
	public function getIterator(): ArrayIterator
	{
		return new ArrayIterator($this->transactions);
	}

	public function toXml(): string
	{
		return ExportXmlGenerator::fromArray($this->toArray());
	}

	/**
	 * @return mixed[]
	 */
	public function toArray(): array
	{
		// Stable sort, insertion order is kept within each type
		$transactions = $this->transactions;
		usort(
			$transactions,
			static fn (Transaction $a, Transaction $b): int => (self::EXPORT_ORDER[$a::NAME] ?? PHP_INT_MAX) <=> (self::EXPORT_ORDER[$b::NAME] ?? PHP_INT_MAX)
		);

		$arr = [];

		foreach ($transactions as $transaction) {
			$arr[] = [$transaction::NAME => $transaction->toArray()];
		}

		return $arr;
	}

}
