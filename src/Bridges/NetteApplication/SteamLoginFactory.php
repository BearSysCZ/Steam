<?php

namespace BearSys\Steam\Bridges\NetteApplication;

class SteamLoginFactory
{
	/**
	 * @param callable $success
	 * @param callable $error
	 * @return SteamLogin
	 */
	public function setup(callable $success, callable $error)
	{
		return new SteamLogin($success, $error);
	}
}