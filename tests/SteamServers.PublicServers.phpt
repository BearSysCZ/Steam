<?php
/**
 * TEST: Public servers test
 *
 * @testCase
 */

require_once 'setup.php';

use Tester\Assert;

class PublicServers extends \Tester\TestCase
{
	/** @var SteamServersTest */
	private $class;


	public function setUp()
	{
		$this->class = new SteamServersTest;
	}


	public function testGet()
	{
		$servers = @$this->class->getPublicServers([
			'appId' => \BearSys\Steam\Games\Battalion1944::GAME_ID,
		]);

		Assert::true(is_array($servers));

		$first = array_shift($servers);
		Assert::count(2, $first); // has to be array with exactly two elements (ip and port)
		Assert::true(\BearSys\Steam\Utils\Validator::isIpValid($first[0])); // has to be valid IP address
		Assert::true(is_numeric($first[1]) && (int) $first[1] > 0 && (int) $first[1] <= 65535); // has to be valid port
	}
}


(new PublicServers())->run();