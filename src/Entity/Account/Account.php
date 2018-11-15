<?php declare(strict_types = 1);

namespace Contributte\Fio\Entity\Account;

use Contributte\Fio\Exceptions\InvalidPropertyException;

/**
 * Account
 */
class Account
{

	/** @var string */
	private $token;

	/** @var string */
	private $accountNum;

	public function __construct(string $token, string $accountNum)
	{
		if (strpos($accountNum, '/') !== false || strlen($accountNum) > 16) {
			throw new InvalidPropertyException('Maximum account length is 16 and must only include digits. Please do not include bank code.');
		}

		if (strlen($token) !== 64) {
			throw new InvalidPropertyException('Token must be exactly 64 characters.');
		}

		$this->accountNum = $accountNum;
		$this->token = $token;
	}

	public function getToken(): string
	{
		return $this->token;
	}

	public function getAccountNum(): string
	{
		return $this->accountNum;
	}

}
