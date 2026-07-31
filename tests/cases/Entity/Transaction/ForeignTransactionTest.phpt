<?php declare(strict_types = 1);

namespace Tests\Cases\Entity\Transaction;

use Contributte\Fio\Entity\Transaction\ForeignTransaction;
use Contributte\Fio\Entity\Transaction\TransactionList;
use Contributte\Fio\Exceptions\InvalidPropertyException;
use Contributte\Tester\Toolkit;
use DateTimeImmutable;
use Tester\Assert;

require_once __DIR__ . '/../../../bootstrap.php';

function createValidForeignTransaction(): ForeignTransaction
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
	$t->setRemittanceInfo1('Payment for hotel 032013');
	$t->setDetailsOfCharges(ForeignTransaction::CHARGES_BEN);
	$t->setPaymentReason(348);

	return $t;
}

// Valid transaction with all mandatory fields
Toolkit::test(function (): void {
	Assert::true(createValidForeignTransaction()->isValid());
});

// Every mandatory field missing invalidates the transaction
Toolkit::test(function (): void {
	$t = new ForeignTransaction();
	$t->setAccountFrom('1234562');
	$t->setAmount(100.00);
	$t->setAccountTo('PK36SCBL0000001123456702');
	$t->setDate(new DateTimeImmutable('2013-04-25'));
	$t->setBenefName('Amir Khan');

	// bic, benefStreet, benefCity, benefCountry, remittanceInfo1, detailsOfCharges, paymentReason missing
	Assert::false($t->isValid());
});

// AccountTo allows non-IBAN account numbers, max 34 alphanumeric
Toolkit::test(function (): void {
	$t = new ForeignTransaction();
	$t->setAccountTo('1234567890');

	Assert::throws(function () use ($t): void {
		$t->setAccountTo(str_repeat('1', 35));
	}, InvalidPropertyException::class);

	Assert::throws(function () use ($t): void {
		$t->setAccountTo('2212-2000000699');
	}, InvalidPropertyException::class);
});

// DetailsOfCharges must be 6 digits
Toolkit::test(function (): void {
	$t = new ForeignTransaction();

	Assert::throws(function () use ($t): void {
		$t->setDetailsOfCharges(4705);
	}, InvalidPropertyException::class);
});

// Full transaction to XML, element named ForeignTransaction
Toolkit::test(function (): void {
	$t = createValidForeignTransaction();
	$t->setComment('Payment a0315');
	$t->setRemittanceInfo4('info4');

	$list = new TransactionList();
	$list->addTransaction($t);
	$xml = $list->toXml();

	Assert::contains('<ForeignTransaction>', $xml);
	Assert::contains('<currency>USD</currency>', $xml);
	Assert::contains('<accountTo>PK36SCBL0000001123456702</accountTo>', $xml);
	Assert::contains('<bic>ALFHPKKAXXX</bic>', $xml);
	Assert::contains('<benefName>Amir Khan</benefName>', $xml);
	Assert::contains('<benefCountry>PK</benefCountry>', $xml);
	Assert::contains('<remittanceInfo1>Payment for hotel 032013</remittanceInfo1>', $xml);
	Assert::contains('<remittanceInfo4>info4</remittanceInfo4>', $xml);
	Assert::contains('<detailsOfCharges>470502</detailsOfCharges>', $xml);
	Assert::contains('<paymentReason>348</paymentReason>', $xml);

	// Optional fields left unset are omitted
	Assert::notContains('<remittanceInfo2>', $xml);
	Assert::notContains('<paymentType>', $xml);
});
