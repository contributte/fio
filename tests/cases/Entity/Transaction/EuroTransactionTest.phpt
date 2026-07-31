<?php declare(strict_types = 1);

namespace Tests\Cases\Entity\Transaction;

use Contributte\Fio\Entity\Transaction\EuroTransaction;
use Contributte\Fio\Entity\Transaction\TransactionList;
use Contributte\Fio\Exceptions\InvalidPropertyException;
use Contributte\Tester\Toolkit;
use DateTimeImmutable;
use Tester\Assert;

require_once __DIR__ . '/../../../bootstrap.php';

function createValidTransaction(): EuroTransaction
{
	$t = new EuroTransaction();
	$t->setAccountFrom('1234562');
	$t->setAmount(100.00);
	$t->setAccountTo('AT611904300234573201');
	$t->setDate(new DateTimeImmutable('2013-04-25'));
	$t->setBenefName('Hans Gruber');

	return $t;
}

// Valid minimal transaction, EUR is default currency
Toolkit::test(function (): void {
	$t = createValidTransaction();

	Assert::true($t->isValid());
	Assert::same('EUR', $t->toArray()['currency']);
});

// Missing benefName
Toolkit::test(function (): void {
	$t = new EuroTransaction();
	$t->setAccountFrom('1234562');
	$t->setAmount(100.00);
	$t->setAccountTo('AT611904300234573201');
	$t->setDate(new DateTimeImmutable('2013-04-25'));

	Assert::false($t->isValid());
});

// Missing accountTo
Toolkit::test(function (): void {
	$t = new EuroTransaction();
	$t->setAccountFrom('1234562');
	$t->setAmount(100.00);
	$t->setDate(new DateTimeImmutable('2013-04-25'));
	$t->setBenefName('Hans Gruber');

	Assert::false($t->isValid());
});

// AccountTo must be IBAN
Toolkit::test(function (): void {
	$t = new EuroTransaction();

	Assert::throws(function () use ($t): void {
		$t->setAccountTo('2212-2000000699');
	}, InvalidPropertyException::class);
});

// Bic must be 11 chars
Toolkit::test(function (): void {
	$t = new EuroTransaction();

	Assert::throws(function () use ($t): void {
		$t->setBic('ABAGATWW');
	}, InvalidPropertyException::class);
});

// BenefName max 35 chars
Toolkit::test(function (): void {
	$t = new EuroTransaction();

	Assert::throws(function () use ($t): void {
		$t->setBenefName(str_repeat('a', 36));
	}, InvalidPropertyException::class);
});

// Comment max 140 chars
Toolkit::test(function (): void {
	$t = new EuroTransaction();

	Assert::throws(function () use ($t): void {
		$t->setComment(str_repeat('a', 141));
	}, InvalidPropertyException::class);
});

// Full transaction to XML, element named T2Transaction
Toolkit::test(function (): void {
	$t = createValidTransaction();
	$t->setKs('0558');
	$t->setVs('1234567890');
	$t->setSs('1234567890');
	$t->setBic('ABAGATWWXXX');
	$t->setComment('Erste Zahlung');
	$t->setBenefStreet('Gugitzgasse 2');
	$t->setBenefCity('Wien');
	$t->setBenefCountry('AT');
	$t->setRemittanceInfo1('info1');
	$t->setPaymentType(EuroTransaction::PAYMENT_TYPE_STANDARD);

	$list = new TransactionList();
	$list->addTransaction($t);
	$xml = $list->toXml();

	Assert::contains('<T2Transaction>', $xml);
	Assert::contains('<accountFrom>1234562</accountFrom>', $xml);
	Assert::contains('<currency>EUR</currency>', $xml);
	Assert::contains('<amount>100</amount>', $xml);
	Assert::contains('<accountTo>AT611904300234573201</accountTo>', $xml);
	Assert::contains('<bic>ABAGATWWXXX</bic>', $xml);
	Assert::contains('<date>2013-04-25</date>', $xml);
	Assert::contains('<benefName>Hans Gruber</benefName>', $xml);
	Assert::contains('<benefCountry>AT</benefCountry>', $xml);
	Assert::contains('<paymentType>431008</paymentType>', $xml);

	// Optional fields left unset are omitted
	Assert::notContains('<remittanceInfo2>', $xml);
	Assert::notContains('<paymentReason>', $xml);
});
