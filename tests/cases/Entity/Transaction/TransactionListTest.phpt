<?php declare(strict_types = 1);

namespace Tests\Cases\Entity\Transaction;

use Contributte\Fio\Entity\Transaction\DomesticTransaction;
use Contributte\Fio\Entity\Transaction\EuroTransaction;
use Contributte\Fio\Entity\Transaction\ForeignTransaction;
use Contributte\Fio\Entity\Transaction\Transaction;
use Contributte\Fio\Entity\Transaction\TransactionList;
use Contributte\Tester\Toolkit;
use DateTimeImmutable;
use Tester\Assert;

require_once __DIR__ . '/../../../bootstrap.php';

function createDomestic(string $vs): DomesticTransaction
{
	$t = new DomesticTransaction();
	$t->setAccountFrom('1234562');
	$t->setAmount(100.00);
	$t->setAccountTo('222444666');
	$t->setBankCode('0300');
	$t->setDate(new DateTimeImmutable('2013-04-25'));
	$t->setVs($vs);

	return $t;
}

function createEuro(): EuroTransaction
{
	$t = new EuroTransaction();
	$t->setAccountFrom('1234562');
	$t->setAmount(100.00);
	$t->setAccountTo('AT611904300234573201');
	$t->setDate(new DateTimeImmutable('2013-04-25'));
	$t->setBenefName('Hans Gruber');

	return $t;
}

function createForeign(): ForeignTransaction
{
	$t = new ForeignTransaction();
	$t->setAccountFrom('1234562');
	$t->setCurrency('USD');
	$t->setAmount(100.00);
	$t->setAccountTo('PK36SCBL0000001123456702');
	$t->setBic('ALFHPKKAXXX');
	$t->setDate(new DateTimeImmutable('2013-04-25'));
	$t->setBenefName('Amir Khan');
	$t->setBenefStreet('Nishtar Rd 13');
	$t->setBenefCity('Karachi');
	$t->setBenefCountry('PK');
	$t->setRemittanceInfo1('Payment for hotel');
	$t->setDetailsOfCharges(ForeignTransaction::CHARGES_SHA);
	$t->setPaymentReason(348);

	return $t;
}

// Export sorts types into the order required by the bank
Toolkit::test(function (): void {
	$list = new TransactionList();
	$list->addTransaction(createForeign());
	$list->addTransaction(createEuro());
	$list->addTransaction(createDomestic('1'));

	$names = array_map(static fn (array $item): string => array_key_first($item), $list->toArray());

	Assert::same(['DomesticTransaction', 'T2Transaction', 'ForeignTransaction'], $names);
});

// Insertion order is kept within the same type (stable sort)
Toolkit::test(function (): void {
	$list = new TransactionList();
	$list->addTransaction(createDomestic('111'));
	$list->addTransaction(createForeign());
	$list->addTransaction(createDomestic('222'));

	$arr = $list->toArray();

	Assert::same('111', $arr[0]['DomesticTransaction']['vs']);
	Assert::same('222', $arr[1]['DomesticTransaction']['vs']);
	Assert::same(['DomesticTransaction', 'DomesticTransaction', 'ForeignTransaction'], array_map(static fn (array $item): string => array_key_first($item), $arr));
});

// Custom subclass sorts by its NAME, not by class
Toolkit::test(function (): void {
	$custom = new class extends Transaction {

		public const NAME = 'DomesticTransaction';

		public function isValid(): bool
		{
			return true;
		}

		/**
		 * @return mixed[]
		 */
		public function toArray(): array
		{
			return array_merge(parent::toArray(), ['date' => $this->date, 'bankCode' => '0300']);
		}

	};
	$custom->setAccountFrom('1234562');
	$custom->setAmount(1.00);
	$custom->setAccountTo('222444666');
	$custom->setDate(new DateTimeImmutable('2013-04-25'));

	$list = new TransactionList();
	$list->addTransaction(createEuro());
	$list->addTransaction($custom);

	$names = array_map(static fn (array $item): string => array_key_first($item), $list->toArray());

	Assert::same(['DomesticTransaction', 'T2Transaction'], $names);
});

// Iterator keeps insertion order
Toolkit::test(function (): void {
	$foreign = createForeign();
	$domestic = createDomestic('1');

	$list = new TransactionList();
	$list->addTransaction($foreign);
	$list->addTransaction($domestic);

	Assert::same([$foreign, $domestic], iterator_to_array($list));
});
