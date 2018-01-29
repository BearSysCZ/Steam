<?php

namespace BearSys\Steam\Bridges\NetteApplication;

use Nette\Application\UI\Component;

class SteamLogin extends Component
{
	/** @var callable */
	private $success;

	/** @var callable */
	private $error;


	/**
	 * SteamLogin constructor.
	 * @param callable $success function (string $steamId)
	 * @param callable $error function (Exception $e)
	 */
	public function __construct(callable $success, callable $error)
	{
		parent::__construct();
		$this->success = $success;
		$this->error = $error;
	}


	public function handleSteamLogin()
	{
		$login = new \Ehesp\SteamLogin\SteamLogin;
		$login->url($this->link('steamLoginReturn!'));
	}


	public function handleSteamLoginReturn()
	{
		$login = new \Ehesp\SteamLogin\SteamLogin;
		try {
			$successCallback = $this->success;
			$successCallback($login->validate());
		} catch (\Exception $e) {
			$errorCallback = $this->error;
			$errorCallback($e);
		}
	}
}