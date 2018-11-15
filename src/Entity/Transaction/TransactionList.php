<?php declare(strict_types = 1);

namespace Contributte\Fio\Entity\Transaction;

use ArrayIterator;
use Contributte\Fio\Utils\ExportXmlGenerator;
use IteratorAggregate;

/**
 * TransactionList
 */
class TransactionList implements IteratorAggregate
{

	/** @var Transaction[] */
	protected $transactions = [];

	public function addTransaction(Transaction $transaction): void
	{
		$this->transactions[] = $transaction;
	}

	/**
	 * @return ArrayIterator|Transaction[]
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
		$arr = [];

		/** @var Transaction $transaction */
		foreach ($this->transactions as $transaction) {
			$arr[] = [$transaction::NAME => $transaction->toArray()];
		}

		return $arr;
	}

}
