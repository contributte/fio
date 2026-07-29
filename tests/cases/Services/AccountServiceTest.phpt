<?php declare(strict_types = 1);

namespace Tests\Cases\Services;

use Contributte\Fio\Entity\Account\Account;
use Contributte\Fio\Exceptions\InvalidResponseException;
use Contributte\Fio\Services\AccountService;
use Contributte\Tester\Toolkit;
use DateTimeImmutable;
use Tester\Assert;
use Tests\Toolkit\SpyHttpClient;

require_once __DIR__ . '/../../bootstrap.php';

const TOKEN = 'foobarfoobarfoobarfoobarfoobarfoobarfoobarfoobarfoobarfoobarfoob';

function createService(SpyHttpClient $http): AccountService
{
	return new AccountService(new Account(TOKEN, '12345667890'), $http);
}

// Movements for period
Toolkit::test(function (): void {
	$xml = (string) file_get_contents(__DIR__ . '/../../fixtures/movements.xml');
	$http = new SpyHttpClient($xml);

	$statement = createService($http)->movementsForPeriod(new DateTimeImmutable('2012-06-26'), new DateTimeImmutable('2012-06-30'));

	Assert::same(
		sprintf('https://fioapi.fio.cz/v1/rest/periods/%s/2012-06-26/2012-06-30/transactions.xml', TOKEN),
		$http->requestedUrl
	);
	Assert::count(2, $statement->getMovements());
});

// Movements from last request
Toolkit::test(function (): void {
	$xml = (string) file_get_contents(__DIR__ . '/../../fixtures/movements.xml');
	$http = new SpyHttpClient($xml);

	createService($http)->movementsFromLastRequest();

	Assert::same(
		sprintf('https://fioapi.fio.cz/v1/rest/last/%s/transactions.xml', TOKEN),
		$http->requestedUrl
	);
});

// Movements for official statement
Toolkit::test(function (): void {
	$xml = (string) file_get_contents(__DIR__ . '/../../fixtures/movements.xml');
	$http = new SpyHttpClient($xml);

	createService($http)->movementsForStatement(2012, 4);

	Assert::same(
		sprintf('https://fioapi.fio.cz/v1/rest/by-id/%s/2012/4/transactions.xml', TOKEN),
		$http->requestedUrl
	);
});

// Set cursor to last downloaded movement ID
Toolkit::test(function (): void {
	$http = new SpyHttpClient();

	createService($http)->setLastId(1147608196);

	Assert::same(
		sprintf('https://fioapi.fio.cz/v1/rest/set-last-id/%s/1147608196/', TOKEN),
		$http->requestedUrl
	);
});

// Set cursor to date
Toolkit::test(function (): void {
	$http = new SpyHttpClient();

	createService($http)->setLastDate(new DateTimeImmutable('2023-07-27'));

	Assert::same(
		sprintf('https://fioapi.fio.cz/v1/rest/set-last-date/%s/2023-07-27/', TOKEN),
		$http->requestedUrl
	);
});

// Invalid response
Toolkit::test(function (): void {
	$http = new SpyHttpClient('not an xml');

	Assert::throws(function () use ($http): void {
		createService($http)->movementsFromLastRequest();
	}, InvalidResponseException::class);
});
