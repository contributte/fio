<?php declare(strict_types = 1);

namespace Contributte\Fio\DI\Nette;

use Contributte\Fio\Config;
use Contributte\Fio\Entity\Account\Account;
use Contributte\Fio\FioManager;
use Contributte\Fio\Http\HttpClient;
use Nette;
use Nette\DI\CompilerExtension;
use Nette\DI\ContainerBuilder;
use Nette\DI\Definitions\Statement;
use Nette\Schema\Expect;

/**
 * FioApiExtension
 */
class FioApiExtension extends CompilerExtension
{

	public function getConfigSchema(): Nette\Schema\Schema
	{
		return Expect::structure([
			'accounts' => Expect::arrayOf(
				Expect::structure([
					'account' => Expect::string()->required(),
					'token' => Expect::string()->required(),
				])
			),
		]);
	}

	/**
	 * Register services
	 */
	public function loadConfiguration(): void
	{
		/** @var ContainerBuilder $builder */
		$builder = $this->getContainerBuilder();

		// Add accounts to config in DI
		$configDef = $builder->addDefinition($this->prefix('config'))
			->setType(Config::class);

		$config = (array) $this->config;
		foreach ($config['accounts'] as $name => $acc) {
			$configDef->addSetup('addAccount', [
				$name,
				new Statement(Account::class, [$acc->token, $acc->account]),
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
