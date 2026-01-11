<?php declare(strict_types = 1);

namespace Tests\Cases\Entity\Transaction;

use Contributte\Fio\Entity\Transaction\DomesticTransaction;
use Contributte\Tester\Toolkit;
use DateTimeImmutable;
use Tester\Assert;

require_once __DIR__ . '/../../../bootstrap.php';

// Missing account from
Toolkit::test(function (): void {
	$t = new DomesticTransaction();
	$t->setAmount(222.22);
	$t->setAccountTo('222444666');
	$t->setBankCode('0300');
	$t->setDate(new DateTimeImmutable());

	Assert::false($t->isValid());
});

// Missing amount
Toolkit::test(function (): void {
	$t = new DomesticTransaction();
	$t->setAccountFrom('111222333444');
	$t->setAccountTo('222444666');
	$t->setBankCode('0300');
	$t->setDate(new DateTimeImmutable());

	Assert::false($t->isValid());
});

// Missing account to
Toolkit::test(function (): void {
	$t = new DomesticTransaction();
	$t->setAccountFrom('111222333444');
	$t->setAmount(222.22);
	$t->setBankCode('0300');
	$t->setDate(new DateTimeImmutable());

	Assert::false($t->isValid());
});

// Missing bank code
Toolkit::test(function (): void {
	$t = new DomesticTransaction();
	$t->setAccountFrom('111222333444');
	$t->setAmount(222.22);
	$t->setAccountTo('222444666');
	$t->setDate(new DateTimeImmutable());

	Assert::false($t->isValid());
});

// Missing date
Toolkit::test(function (): void {
	$t = new DomesticTransaction();
	$t->setAccountFrom('111222333444');
	$t->setAmount(222.22);
	$t->setAccountTo('222444666');
	$t->setBankCode('0300');

	Assert::false($t->isValid());
});

// Valid
Toolkit::test(function (): void {
	$t = new DomesticTransaction();
	$t->setAccountFrom('111222333444');
	$t->setAmount(222.22);
	$t->setAccountTo('222444666');
	$t->setBankCode('0300');
	$t->setDate(new DateTimeImmutable());

	Assert::true($t->isValid());
});
