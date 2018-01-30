<?php

namespace BearSys\Steam\Bridges\NetteApplication;

use Nette\Application\UI\Component;

class SteamLogin extends Component
{
	/** @var callable */
	private $callback;


	/**
	 * SteamLogin constructor.
	 * @param callable $callback function (string $steamId)
	 */
	public function __construct(callable $callback)
	{
		parent::__construct();
		$this->callback = $callback;
	}


	public function handleSteamLogin()
	{
		$login = new \Ehesp\SteamLogin\SteamLogin;
		$login->url($this->link('steamLoginReturn!'));
	}


	public function handleSteamLoginReturn()
	{
		$login = new \Ehesp\SteamLogin\SteamLogin;
		$callback = $this->callback;
		$callback($login);
	}
}