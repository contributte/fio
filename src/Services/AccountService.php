<?php declare(strict_types = 1);

namespace Markette\Fio\Services;

use Markette\Fio\Http\Request;

/**
 * AccountService
 *
 * @author Filip Suska <vody105@gmail.com>
 */
class AccountService extends Service
{

    /**
     * @param string $from
     * @param string $to
     * @return void
     */
    public function movementsForPeriod(string $from, string $to): void
    {
        // Tady se vytvori request a $this->sendRequest()
    }

    /**
     * @return void
     */
    public function movementsFromLastRequest(): void
    {
        // Tady se vytvori request a $this->sendRequest()
        // a dalsi tyto metody
    }

    /**
     * @param Request $request
     * @return string
     */
    protected function createRequest(Request $request): string
    {
        // TODO: Implement sendRequest() method.
        return '';
    }

}
