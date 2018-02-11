<?php

namespace BearSys\Steam\Games;

class Battalion1944 implements IGame
{
	const GAME_ID = 489940;

	/**
	 * @param \GameServer $server
	 * @param string|NULL $ip
	 * @return \stdClass
	 */
	public function retrieveData($server, $ip, $queryPort)
	{
		$info = [
			'info' => $server->getServerInfo(),
			'rules' => $server->getRules(),
			'players' => $server->getPlayers(),
		];
		$info = json_decode(json_encode($info));

		$parsedInfo = (object) [
			'ip' => $ip,
			'port' => $info->info->serverPort,
			'queryPort' => $queryPort,
			'serverName' => substr($info->rules->bat_name_s, 0, strpos($info->rules->bat_name_s, ' (RELEASE')),
			'map' => $info->info->mapName,
			'mode' => $info->info->gameDir,
			'private' => $info->rules->bat_has_password_s === 'Y' ? TRUE : FALSE,
			'playersCount' => $info->rules->bat_player_count_s,
			'playersMax' => $info->rules->bat_max_players_i,
			'players' => $info->players,
		];

		return $parsedInfo;
	}
}