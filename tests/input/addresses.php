<?php
return [
	[
		'input' => [
			'ip' => '123.123.123.123:7890',
			'port' => NULL,
		],
		'output' => [
			'ip' => '123.123.123.123',
			'port' => 7890,
		]
	],
	[
		'input' => [
			'ip' => '123.123.123.123',
			'port' => NULL,
		],
		'output' => [
			'ip' => '123.123.123.123',
			'port' => \BearSys\Steam\SteamServers::PORT_DEFAULT,
		]
	],
	[
		'input' => [
			'ip' => '123.123.123.123',
			'port' => 7890,
		],
		'output' => [
			'ip' => '123.123.123.123',
			'port' => 7890,
		]
	],
	[
		'input' => [
			'ip' => '123.123.123.123:7890',
			'port' => 987,
		],
		'output' => [
			'ip' => '123.123.123.123',
			'port' => 7890,
		]
	],
	[
		'input' => [
			'ip' => 'necroraisers.com',
			'port' => NULL,
		],
		'output' => [
			'ip' => '37.187.56.91',
			'port' => 7777,
		]
	]
];