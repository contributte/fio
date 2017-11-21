<?php declare(strict_types = 1);

namespace Markette\Fio\Services;

use Markette\Fio\Entity\Account\Account;
use Markette\Fio\Http\IHttpClient;
use Markette\Fio\Http\Request;

/**
 * Service
 *
 * @author Filip Suska <vody105@gmail.com>
 */
abstract class Service
{

    /** @var IHttpClient */
    protected $httpClient;

    /** @var Account */
    protected $account;

    /**
     * @param Account $account
     * @param IHttpClient $httpClient
     */
    public function __construct(Account $account, IHttpClient $httpClient)
    {
        $this->httpClient = $httpClient;
        $this->account = $account;
    }

    /**
     * @param Request $request
     * @return string
     */
    abstract protected function createRequest(Request $request): string;

}
