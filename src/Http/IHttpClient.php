<?php declare(strict_types = 1);

namespace Markette\Fio\Http;

/**
 * Interface IHttpClient
 *
 * @author Filip Suska <vody105@gmail.com>
 */
interface IHttpClient
{

    /**
     * @param Request $request
     * @return string
     */
    public function sendRequest(Request $request): string;

}
