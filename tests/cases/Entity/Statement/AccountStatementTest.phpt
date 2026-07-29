<?php declare(strict_types = 1);

namespace Tests\Cases\Entity\Statement;

use Contributte\Fio\Entity\Statement\AccountStatement;
use Contributte\Fio\Exceptions\InvalidResponseException;
use Contributte\Tester\Toolkit;
use Tester\Assert;

require_once __DIR__ . '/../../../bootstrap.php';

// Parse valid XML
Toolkit::test(function (): void {
	$xml = (string) file_get_contents(__DIR__ . '/../../../fixtures/movements.xml');
	$statement = AccountStatement::fromXml($xml);

	Assert::same('2400222222', $statement->getAccountId());
	Assert::same('2010', $statement->getBankId());
	Assert::same('CZK', $statement->getCurrency());
	Assert::same('CZ7920100000002400222222', $statement->getIban());
	Assert::same('FIOBCZPPXXX', $statement->getBic());
	Assert::same(195.00, $statement->getOpeningBalance());
	Assert::same(195.01, $statement->getClosingBalance());
	Assert::same(1148734530, $statement->getIdFrom());
	Assert::same(1149190193, $statement->getIdTo());
	Assert::same(1149190192, $statement->getIdLastDownload());
	Assert::null($statement->getYearList());
	Assert::null($statement->getIdList());
	Assert::same('2012-06-26', $statement->getDateStart()?->format('Y-m-d'));
	Assert::same('2012-06-30', $statement->getDateEnd()?->format('Y-m-d'));

	$movements = $statement->getMovements();
	Assert::count(2, $movements);

	$first = $movements[0];
	Assert::same(1148734530, $first->getId());
	Assert::same('2012-06-26', $first->getDate()->format('Y-m-d'));
	Assert::same(1.00, $first->getAmount());
	Assert::same('CZK', $first->getCurrency());
	Assert::same('Příjem převodem uvnitř banky', $first->getType());
	Assert::same('2900233333', $first->getCounterAccount());
	Assert::same('Pavel, Novák', $first->getCounterAccountName());
	Assert::same('2010', $first->getBankCode());
	Assert::same('Fio banka, a.s.', $first->getBankName());
	Assert::same('0558', $first->getKs());
	Assert::same('1234567890', $first->getVs());
	Assert::null($first->getSs());
	Assert::same(2105685816, $first->getInstructionId());

	$second = $movements[1];
	Assert::same(1149190193, $second->getId());
	Assert::same(0.01, $second->getAmount());
	Assert::same('Připsaný úrok', $second->getType());
	Assert::null($second->getCounterAccount());
	Assert::null($second->getBankCode());

	// Statement is iterable over movements
	Assert::same($movements, iterator_to_array($statement));
});

// Empty transaction list (only info header)
Toolkit::test(function (): void {
	$xml = '<?xml version="1.0" encoding="UTF-8"?><AccountStatement><Info><accountId>2400222222</accountId><bankId>2010</bankId><currency>CZK</currency><iban>CZ7920100000002400222222</iban><bic>FIOBCZPPXXX</bic><openingBalance>1.0</openingBalance><closingBalance>1.0</closingBalance></Info><TransactionList/></AccountStatement>';
	$statement = AccountStatement::fromXml($xml);

	Assert::count(0, $statement->getMovements());
});

// Invalid XML
Toolkit::test(function (): void {
	Assert::throws(function (): void {
		AccountStatement::fromXml('this is not xml');
	}, InvalidResponseException::class);
});

// Unexpected structure
Toolkit::test(function (): void {
	Assert::throws(function (): void {
		AccountStatement::fromXml('<foo>bar</foo>');
	}, InvalidResponseException::class, 'Unexpected XML structure.');
});
