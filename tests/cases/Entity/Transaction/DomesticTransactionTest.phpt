<?php declare(strict_types = 1);

use Markette\Fio\Entity\Transaction\DomesticTransaction;
use Tester\Assert;

require_once __DIR__ . '/../../../bootstrap.php';

// Missing account from
test(function (): void {
	$t = new DomesticTransaction();
	$t->setAmount(222.22);
	$t->setAccountTo('222444666');
	$t->setBankCode('0300');
	$t->setDate(new DateTimeImmutable());

	Assert::false($t->isValid());
});

// Missing amount
test(function (): void {
	$t = new DomesticTransaction();
	$t->setAccountFrom('111222333444');
	$t->setAccountTo('222444666');
	$t->setBankCode('0300');
	$t->setDate(new DateTimeImmutable());

	Assert::false($t->isValid());
});

// Missing account to
test(function (): void {
	$t = new DomesticTransaction();
	$t->setAccountFrom('111222333444');
	$t->setAmount(222.22);
	$t->setBankCode('0300');
	$t->setDate(new DateTimeImmutable());

	Assert::false($t->isValid());
});

// Missing bank code
test(function (): void {
	$t = new DomesticTransaction();
	$t->setAccountFrom('111222333444');
	$t->setAmount(222.22);
	$t->setAccountTo('222444666');
	$t->setDate(new DateTimeImmutable());

	Assert::false($t->isValid());
});

// Missing date
test(function (): void {
	$t = new DomesticTransaction();
	$t->setAccountFrom('111222333444');
	$t->setAmount(222.22);
	$t->setAccountTo('222444666');
	$t->setBankCode('0300');

	Assert::false($t->isValid());
});

// Valid
test(function (): void {
	$t = new DomesticTransaction();
	$t->setAccountFrom('111222333444');
	$t->setAmount(222.22);
	$t->setAccountTo('222444666');
	$t->setBankCode('0300');
	$t->setDate(new DateTimeImmutable());

	Assert::true($t->isValid());
});
