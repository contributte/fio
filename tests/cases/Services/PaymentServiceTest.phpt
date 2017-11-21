<?php declare(strict_types = 1);

use Markette\Fio\Entity\Account\Account;
use Markette\Fio\Entity\Transaction\DomesticTransaction;
use Markette\Fio\Exceptions\InvalidTransactionException;
use Markette\Fio\Http\IHttpClient;
use Markette\Fio\Services\PaymentService;
use Tester\Assert;

require_once __DIR__ . '/../../bootstrap.php';

// Validation
test(function (): void {
	$http = Mockery::mock(IHttpClient::class);
	$acc = new Account('foobarfoobarfoobarfoobarfoobarfoobarfoobarfoobarfoobarfoobarfoob', '12345667890');

	$service = new PaymentService($acc, $http);
	$t = new DomesticTransaction();

	Assert::throws(function () use ($service, $t): void {
		$service->addPayment($t);
	}, InvalidTransactionException::class);
});
