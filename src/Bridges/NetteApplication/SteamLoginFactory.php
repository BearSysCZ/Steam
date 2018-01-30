<?php

namespace BearSys\Steam\Bridges\NetteApplication;

class SteamLoginFactory
{
	/**
	 * @param callable $callback
	 * @return SteamLogin
	 */
	public function setup(callable $callback)
	{
		return new SteamLogin($callback);
	}
}