<?php declare(strict_types = 1);

namespace Contributte\Fio\DI\Nette;

use Contributte\Fio\Config;
use Contributte\Fio\Entity\Account\Account;
use Contributte\Fio\FioManager;
use Contributte\Fio\Http\HttpClient;
use Nette\DI\CompilerExtension;
use Nette\DI\ContainerBuilder;
use Nette\DI\Statement;
use Nette\Utils\Validators;

/**
 * FioApiExtension
 *
 * @author Filip Suska <vody105@gmail.com>
 */
class FioApiExtension extends CompilerExtension
{

	/** @var string[] */
	private $defaults = [
		'accounts' => [],
	];

	/**
	 * Register services
	 *
	 * @return void
	 */
	public function loadConfiguration(): void
	{
		$neon = $this->validateConfig($this->defaults);

		/** @var ContainerBuilder $builder */
		$builder = $this->getContainerBuilder();

		// Input validation
		Validators::assertField($neon, 'accounts', 'array');

		// Add accounts to config in DI
		$configDef = $builder->addDefinition($this->prefix('config'))
			->setType(Config::class);

		foreach ($neon['accounts'] as $name => $acc) {
			// Input validation
			Validators::assertField($acc, 'token', 'string:64');
			Validators::assertField($acc, 'account', 'string:..16');

			$configDef->addSetup('addAccount', [
				$name,
				new Statement(Account::class, [$acc['token'], $acc['account']]),
			]);
		}

		// Add FioManager to DI
		$builder->addDefinition($this->prefix('manager'))
			->setFactory(FioManager::class, [
				$configDef,
				new Statement(HttpClient::class),
			]);
	}

}
