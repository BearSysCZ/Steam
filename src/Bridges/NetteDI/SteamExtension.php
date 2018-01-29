<?php

namespace BearSys\Steam\Bridges\NetteDI;

use Nette\DI\CompilerExtension;
use BearSys\Steam\SteamWrapper;
use BearSys\Steam\Bridges\NetteApplication\SteamLoginFactory;

class SteamExtension extends CompilerExtension
{
	/**
	 * @var array
	 */
	public $defaults = [
		'apiKey' => NULL
	];


	public function __construct()
	{
	}


	public function loadConfiguration()
	{
		$builder = $this->getContainerBuilder();
		$config = $this->validateConfig($this->defaults);

		$builder->addDefinition($this->prefix('wrapper'))
			->setFactory(SteamWrapper::class, [$config['apiKey']]);

		$builder->addDefinition($this->prefix('login'))
			->setFactory(SteamLoginFactory::class);
	}
}