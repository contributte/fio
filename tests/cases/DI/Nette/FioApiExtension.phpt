<?php declare(strict_types = 1);

use Contributte\Fio\DI\Nette\FioApiExtension;
use Contributte\Fio\FioManager;
use Nette\DI\Compiler;
use Nette\DI\Container;
use Nette\DI\ContainerLoader;
use Tester\Assert;

require_once __DIR__ . '/../../../bootstrap.php';

// Test if FioManager and Config is created
test(function (): void {
	$loader = new ContainerLoader(TEMP_DIR, TRUE);
	$class = $loader->load(function (Compiler $compiler): void {
		$compiler->addExtension('fio', new FioApiExtension())
			->addConfig([
				'fio' => [
					'accounts' => [
						'acc1' => [
							'account' => '1234567890',
							'token' => 'foobarfoobarfoobarfoobarfoobarfoobarfoobarfoobarfoobarfoobarfoob',
						],
						'acc2' => [
							'account' => '1234567890',
							'token' => 'foobarfoobarfoobarfoobarfoobarfoobarfoobarfoobarfoobarfoobarfoob',
						],
					],
				],
			]);
	}, 1);

	/** @var Container $container */
	$container = new $class();

	// Service created
	Assert::type(FioManager::class, $container->getService('fio.manager'));
	// We have two accounts
	Assert::count(2, $container->getService('fio.config')->getAccounts());
});
