<?php declare(strict_types = 1);

namespace Tests\Cases\Exceptions;

use Contributte\Fio\Exceptions\HttpStatusException;
use Contributte\Tester\Toolkit;
use Tester\Assert;

require_once __DIR__ . '/../../bootstrap.php';

// Known status with hint
Toolkit::test(function (): void {
	$e = HttpStatusException::fromStatusCode(409, 'body');

	Assert::same(409, $e->getStatusCode());
	Assert::same(409, $e->getCode());
	Assert::same('body', $e->getResult());
	Assert::same('Server returned HTTP status 409. Minimum interval of 30 seconds between requests with the same token was not respected.', $e->getMessage());
});

// Unknown status without hint
Toolkit::test(function (): void {
	$e = HttpStatusException::fromStatusCode(503);

	Assert::same(503, $e->getStatusCode());
	Assert::null($e->getResult());
	Assert::same('Server returned HTTP status 503.', $e->getMessage());
});
