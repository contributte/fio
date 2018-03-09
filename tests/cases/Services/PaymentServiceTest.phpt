<?php declare(strict_types = 1);

use Contributte\Fio\Entity\Account\Account;
use Contributte\Fio\Entity\Transaction\DomesticTransaction;
use Contributte\Fio\Exceptions\InvalidTransactionException;
use Contributte\Fio\Http\IHttpClient;
use Contributte\Fio\Services\PaymentService;
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
