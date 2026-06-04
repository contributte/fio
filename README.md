![](https://heatbadger.now.sh/github/readme/contributte/fio/)

<p align=center>
  <a href="https://github.com/contributte/fio/actions"><img src="https://badgen.net/github/checks/contributte/fio/master?cache=300"></a>
  <a href="https://coveralls.io/r/contributte/fio"><img src="https://badgen.net/coveralls/c/github/contributte/fio?cache=300"></a>
  <a href="https://packagist.org/packages/contributte/fio"><img src="https://badgen.net/packagist/dm/contributte/fio"></a>
  <a href="https://packagist.org/packages/contributte/fio"><img src="https://badgen.net/packagist/v/contributte/fio"></a>
</p>
<p align=center>
  <a href="https://packagist.org/packages/contributte/fio"><img src="https://badgen.net/packagist/php/contributte/fio"></a>
  <a href="https://github.com/contributte/fio"><img src="https://badgen.net/github/license/contributte/fio"></a>
  <a href="https://bit.ly/ctteg"><img src="https://badgen.net/badge/support/gitter/cyan"></a>
  <a href="https://bit.ly/cttfo"><img src="https://badgen.net/badge/support/forum/yellow"></a>
  <a href="https://contributte.org/partners.html"><img src="https://badgen.net/badge/sponsor/donations/F96854"></a>
</p>

<p align=center>
Website 🚀 <a href="https://contributte.org">contributte.org</a> | Contact 👨🏻‍💻 <a href="https://f3l1x.io">f3l1x.io</a> | Twitter 🐦 <a href="https://twitter.com/contributte">@contributte</a>
</p>

Fio Bank API integration for Nette applications. Configure your Fio accounts and send domestic payments through a simple payment service.

## Versions

| State       | Version | Branch   | PHP      |
|-------------|---------|----------|----------|
| development | `^0.4`  | `master` | `>= 7.1` |
| stable      | `^0.3`  | `master` | `>= 7.1` |

## Installation

To install latest version of `contributte/fio` use [Composer](https://getcomposer.org).

```bash
composer require contributte/fio
```

## Requirements

You need your bank account and token generated in your e-banking. There is no special token for testing, so you have to use the same token for both testing and production. It is recommended to generate two tokens per account: one for sending payments to the bank and one for downloading payments from the bank. This package currently includes only sending payments.

- **accountNumber**
- **accountToken**

## Configuration

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

- Sender account (automatically supplied from config)
- Currency
- Amount
- Recipient account
- Bank code
- Date

For more properties check the class itself.

From `FioManager` you get `PaymentService` which has two methods:

- `addPayment(Transaction $t)` - adds and verifies transaction
- `sendPayments()` - sends added transaction to bank and returns PaymentResponse

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
			$transaction->setMessageForRecipient($p[5]); // string

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

## Development

See [how to contribute](https://contributte.org) to this package. This package is currently maintained by these authors.

<a href="https://github.com/f3l1x">
    <img width="80" height="80" src="https://avatars2.githubusercontent.com/u/538058?v=3&s=80">
</a>

-----

Consider to [support](https://contributte.org/partners.html) **contributte** development team.
Also thank you for using this package.
