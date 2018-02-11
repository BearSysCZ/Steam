<?php
return [
	[
		'input' => [
			'ip' => 'invalid-domain',
			'port' => 'not-even-gets-here',
		],
		'error' => \BearSys\Steam\SteamServers::EXCEPTION_CODE_INVALID_IP,
	],
	[
		'input' => [
			'ip' => '123.456.789.012', // invalid IP, not between 0-255
			'port' => 'not-even-gets-here',
		],
		'error' => \BearSys\Steam\SteamServers::EXCEPTION_CODE_INVALID_IP,
	],
	[
	'input' => [
			'ip' => 'necroraisers.com',
			'port' => 'gets-here',
		],
		'error' => \BearSys\Steam\SteamServers::EXCEPTION_CODE_INVALID_PORT,
	],
	[
		'input' => [
			'ip' => 'test.necroraisers.com',
			'port' => 'gets-here',
		],
		'error' => \BearSys\Steam\SteamServers::EXCEPTION_CODE_INVALID_PORT,
	],
];