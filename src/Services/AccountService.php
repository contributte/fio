<?php declare(strict_types = 1);

namespace Contributte\Fio\Services;

use Contributte\Fio\Http\Request;

/**
 * AccountService
 */
class AccountService extends Service
{

	public function movementsForPeriod(string $from, string $to): void
	{
		// Tady se vytvori request a $this->sendRequest()
	}

	public function movementsFromLastRequest(): void
	{
		// Tady se vytvori request a $this->sendRequest()
		// a dalsi tyto metody
	}

	protected function createRequest(Request $request): string
	{
		// TODO: Implement sendRequest() method.
		return '';
	}

}
