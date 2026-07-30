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

// Valid minimal transaction can be exported without optional setters
Toolkit::test(function (): void {
	$t = new DomesticTransaction();
	$t->setAccountFrom('111222333444');
	$t->setAmount(222.22);
	$t->setAccountTo('222444666');
	$t->setBankCode('0300');
	$t->setDate(new DateTimeImmutable('2026-07-30'));

	Assert::true($t->isValid());
	Assert::noError(function () use ($t): void {
		$t->toArray();
	});

	$array = $t->toArray();
	Assert::same('111222333444', $array['accountFrom']);
	Assert::same(222.22, $array['amount']);
	Assert::same('222444666', $array['accountTo']);
	Assert::same('0300', $array['bankCode']);
	Assert::same('2026-07-30', $array['date']);
	Assert::null($array['ks']);
	Assert::null($array['vs']);
	Assert::null($array['ss']);
	Assert::null($array['messageForRecipient']);
	Assert::null($array['comment']);
	Assert::null($array['paymentReason']);
	Assert::null($array['paymentType']);
});
