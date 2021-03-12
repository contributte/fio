# Contributte Fio Api Integration

## Content

- [Requirements - what do you need](#requirements)
- [Installation - how to register an extension](#installation)
- [Usage - how to use it](#usage)

## Requirements

You need your bank account and token generated in your e-banking. There is no special token for testing. So you will have to use the same token for both testing and production. It is recommended to generate two tokens per account. One for sending payments to bank and the other one for downloading payments from bank (We only included sending payments feature yet.).

* **accountNumber**
* **accountToken**

## Installation

```neon
extensions:
	fio: Contributte\Fio\DI\Nette\FioApiExtension

fio:
	accounts:
		czk-write:
			account: %fioapi.account1%
			token: %fioapi.token1%
		czk-read:
			account: %fioapi.account2%
			token: %fioapi.token2%
```

## Usage

As this package is under development we haven't included downloading payments from bank yet. You can use our package for simply sending domestic payments to bank. You can send multiple payments at once but you can query bank api only once in 30 seconds (if you ping servers in shorter intervals it will return error). This package only supports domestic transactions but it is very easy to implement other types of payments. You only need to extend `Contributte\Fio\Entity\Transaction\Transaction` class and use it same way as Domestic Transaction class which has these mandatory properties:

* Sender account (automatically supplier from config)
* Currency
* Amount
* Recipient account
* Bank code
* Date

For more properties check the class itself.

From `FioManager` you get `PaymentService` which has two methods:

* `addPayment(Transaction $t)` - adds and verifies transaction
* `sendPayments()` - sends added transaction to bank and return PaymentResponse

```php
<?php

namespace App;

use Contributte\Fio\Entity\Transaction\DomesticTransaction;
use Contributte\Fio\Exceptions\InvalidResponseException;
use Contributte\Fio\FioManager;
use Tracy\Debugger;

final class SendPaymentsControl extends BaseControl
{

	private $fioManager;

	public function __construct(FioManager $fioManager)
	{
		parent::__construct();
		$this->fioManager = $fioManager;
	}

	public function handleSend(): void
	{
		// You somehow get your payments
		$paymentsToSend = $this->findPayments();

		// Get our payment service with proper account name
		$paymentService = $this->fioManager->createPaymentService('czk-write');

		foreach ($paymentsToSend as $p) {
			// Create transaction object and fill it with data
			$transaction = new DomesticTransaction();
			$transaction->setAccountTo($p[0]); // string
			$transaction->setBankCode($p[1]); // string
			$transaction->setVs($p[2]); // string
			$transaction->setAmount($p[3]); // float
			$transaction->setDate($p[4]); // DateTimeInterface
			$transaction->setMessageForRecipient($[5]); // string

			$paymentService->addPayment($transaction);
		}

		try {
			// Send payments to bank
			$response = $paymentService->sendPayments();

		} catch (InvalidResponseException $e) {
			// Bank returned unknown format of the response but you can get
			// pure response by calling $e->getResult() and manually check what went wrong
			Debugger::log($e->getResult());
		}

		// If response is in known format, check response from bank
		if ($response->isOk()) {
			// Great do whatever you want
		} else {
			// $response->getErrorCodeMessage()
		}
	}

}
```
