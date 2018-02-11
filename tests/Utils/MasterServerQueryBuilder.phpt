<?php
/**
 * TEST: Master Server query builder test
 *
 * @testCase
 */

require_once __DIR__ . '/../setup.php';

use Tester\Assert;

class MasterServerQueryBuilder extends \Tester\TestCase
{
	public function testBuilder()
	{
		$builder = new \BearSys\Steam\Utils\MasterServerQueryBuilder();

		$builder->setAppId(\BearSys\Steam\Games\Battalion1944::GAME_ID);
		Assert::same('\\appid\\' . \BearSys\Steam\Games\Battalion1944::GAME_ID, $builder->getQuery());

		// TODO
	}
}

(new MasterServerQueryBuilder())->run();
