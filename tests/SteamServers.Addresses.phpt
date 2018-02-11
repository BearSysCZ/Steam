<?php
/**
 * TEST: Addresses test
 *
 * @testCase
 */

require_once 'setup.php';

use Tester\Assert;

class Addresses extends \Tester\TestCase
{
	/** @var SteamServersTest */
	private $class;


	public function setUp()
	{
		$this->class = new SteamServersTest;
	}


	public function testValid()
	{
		$addresses = require 'input/addresses.php';
		foreach ($addresses as $address) {
			$result = $this->class->parseAddressResult($address['input']['ip'], $address['input']['port']);
			Assert::same($address['output']['ip'], $result->ip);
			Assert::same($address['output']['port'], $result->port);
		}
	}


	public function testInvalid()
	{
		$invalidAddresses = require 'input/invalidAddresses.php';
		foreach ($invalidAddresses as $address) {
			Assert::exception(function () use ($address) {
				$this->class->parseAddressResult($address['input']['ip'], $address['input']['port']);
			}, \BearSys\Steam\SteamException::class, NULL, $address['error']);
		}
	}
}

(new Addresses())->run();
