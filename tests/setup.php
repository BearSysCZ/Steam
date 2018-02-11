<?php

require __DIR__ . '/../vendor/autoload.php';
Tester\Environment::setup();

// mocked class

class SteamServersTest extends \BearSys\Steam\SteamServers
{
	public function parseAddressResult($ip, $port)
	{
		return $this->parseAddress($ip, $port);
	}
}
