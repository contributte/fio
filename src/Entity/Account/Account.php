<?php declare(strict_types = 1);

namespace Markette\Fio\Entity\Account;

use Markette\Fio\Exceptions\InvalidPropertyException;

/**
 * Account
 *
 * @author Filip Suska <vody105@gmail.com>
 */
class Account
{

    /** @var string */
    private $token;

    /** @var string */
    private $accountNum;

    /**
     * @param string $token
     * @param string $accountNum
     */
    public function __construct(string $token, string $accountNum)
    {
        if (strpos($accountNum, '/') || strlen($accountNum) > 16) {
            throw new InvalidPropertyException('Maximum account length is 16 and must only include digits. Please do not include bank code.');
        }

        if (strlen($token) !== 64) {
            throw new InvalidPropertyException('Token must be exactly 64 characters.');
        }

        $this->accountNum = $accountNum;
        $this->token = $token;
    }

    /**
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * @return string
     */
    public function getAccountNum(): string
    {
        return $this->accountNum;
    }

}
