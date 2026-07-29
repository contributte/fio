# Contributte Fio Api Integration

## Content

- [Requirements - what do you need](#requirements)
- [Installation - how to register an extension](#installation)
- [Usage - how to use it](#usage)

## Requirements

You need your bank account and token generated in your e-banking. There is no special token for testing. So you will have to use the same token for both testing and production. It is recommended to generate two tokens per account. One for sending payments to bank and the other one for downloading payments from bank.

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

You can use our package for sending payments to bank and for downloading account movements (statements). You can send multiple payments at once but you can query bank api only once in 30 seconds (if you ping servers in shorter intervals it will return error). This package supports domestic transactions (`DomesticTransaction`), euro payments (`EuroTransaction`) and foreign payments (`ForeignTransaction`).

`DomesticTransaction` mandatory properties:

* Sender account (automatically supplied from config)
* Currency (default CZK)
* Amount
* Recipient account
* Bank code
* Date

`EuroTransaction` mandatory properties:

* Sender account (automatically supplied from config)
* Currency (default EUR)
* Amount
* Recipient account (IBAN)
* Date
* Beneficiary name (`setBenefName()`)

`ForeignTransaction` mandatory properties:

* Sender account (automatically supplied from config)
* Currency (account currency, default CZK)
* Amount
* Recipient account (account number or IBAN)
* BIC
* Date
* Beneficiary name, street, city and country
* Remittance info 1 (`setRemittanceInfo1()`)
* Details of charges (`setDetailsOfCharges()`, `CHARGES_OUR`/`CHARGES_BEN`/`CHARGES_SHA`)
* Payment reason (`setPaymentReason()`, see FIO API docs chapter 6.3.4)

The bank requires transaction types in a fixed order within one batch (domestic, euro, foreign payments) — the library sorts the export automatically, you can add payments in any order.

For more properties check the classes themselves.

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

### Downloading movements

From `FioManager` you get `AccountService` which downloads account movements in Fio XML format and parses them into `AccountStatement` (account info + list of `Movement` objects):

* `movementsForPeriod(DateTimeInterface $from, DateTimeInterface $to)` - movements for given period
* `movementsFromLastRequest()` - movements since the last download (bank moves the cursor automatically)
* `movementsForStatement(int $year, int $id)` - official statement by year and number
* `setLastId(int $id)` - sets the cursor to the ID of the last successfully downloaded movement
* `setLastDate(DateTimeInterface $date)` - sets the cursor to a date

```php
<?php

$accountService = $fioManager->createAccountService('czk-read');

$statement = $accountService->movementsForPeriod(
	new DateTimeImmutable('2023-08-01'),
	new DateTimeImmutable('2023-08-31')
);

echo $statement->getOpeningBalance();
echo $statement->getClosingBalance();

foreach ($statement as $movement) {
	echo $movement->getId();
	echo $movement->getDate()->format('Y-m-d');
	echo $movement->getAmount();
	echo $movement->getCurrency();
	echo $movement->getType();
	echo $movement->getVs();
}
```

Data older than 90 days require temporarily unlocking full history in your e-banking (Settings → API).
