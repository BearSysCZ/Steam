<?php

namespace BearSys\Steam;

use BearSys\Steam\Games\IGame;
use BearSys\Steam\Utils\MasterServerQueryBuilder;
use BearSys\Steam\Utils\Validator;
use MasterServer;
use Nette\Reflection\ClassType;
use SourceServer;

class SteamServers
{
	const PORT_DEFAULT = 7777;

	const EXCEPTION_CODE_INVALID_IP = 1;
	const EXCEPTION_CODE_INVALID_PORT = 2;

	const EXCEPTION_MESSAGE_INVALID_IP = 'Invalid IP address entered.';
	const EXCEPTION_MESSAGE_INVALID_PORT = 'Invalid port entered.';


	/**
	 * Queries Steam public servers from Steam Master Server.
	 *
	 * @param array $options Use MasterServerQueryBuilder::OPTION_*
	 * @param string $address Use MasterServer::*_MASTER_SERVER
	 * @param int $region Use MasterServer::REGION_*
	 * @return array
	 */
	public function getPublicServers(array $options = [], $address = MasterServer::SOURCE_MASTER_SERVER, $region = MasterServer::REGION_ALL)
	{
		$master = new MasterServer($address);

		$queryBuilder = new MasterServerQueryBuilder();
		foreach ($options as $option => $value) {
			$method = 'set' . ucfirst($option);
			if (method_exists($queryBuilder, $method))
				$queryBuilder->$method($value);
		}
		print_r($queryBuilder->getQuery());
		$servers = $master->getServers($region, $queryBuilder->getQuery());

		return $servers;
	}


	/**
	 * Gets info about server using game port.
	 *
	 * @param string $ip Either 'ip:port' or just 'ip'. IP could be also DNS name.
	 * @param int|string|null $port When empty and $ip is not 'ip:port', then 7777 will be used
	 * @param string $game
	 * @return \stdClass|NULL
	 * @throws SteamException
	 */
	public function getInfoAboutServer($ip, $port = NULL, $game)
	{
		$game = new $game;
		if (!$game instanceof IGame)
			throw new SteamException('Game has to implement BearSys\SteamServers\Games\IGame interface.');

		$address = $this->parseAddress($ip, $port);
		$ip = $address->ip;
		$port = $address->port;

		$serversAtIp = $this->getServersAtIp($ip);

		foreach ($serversAtIp as $server) {
			$queried = $this->queryServer($server[0], $server[1], get_class($game));
			if ($queried->port === $port)
				return $queried;
		}
		return NULL;
	}


	/**
	 * Gets info about server using query port.
	 *
	 * @param $ip
	 * @param $queryPort
	 * @param $game
	 * @param string $type
	 * @return mixed
	 * @throws SteamException
	 */
	public function queryServer($ip, $queryPort, $game, $type = SourceServer::class)
	{
		$address = $this->parseAddress($ip, $queryPort);
		$ip = $address->ip;
		$queryPort = $address->port;

		$game = new $game;
		if (!$game instanceof IGame)
			throw new SteamException('Game has to implement BearSys\SteamServers\Games\IGame interface.');

		$typeReflection = ClassType::from($type);
		if (!$typeReflection->is(\Server::class))
			throw new SteamException('Type class has to be child of Server class.');

		/** @var \GameServer $svr */
		$svr = new $type($ip, $queryPort);

		return $game->retrieveData($svr, $ip, $queryPort);
	}


	protected function parseAddress($ip, $port)
	{
		if (strpos($ip, ':'))
			$address = explode(':', $ip);
		elseif ($port !== NULL)
			$address = [$ip, $port];
		else
			$address = [$ip, self::PORT_DEFAULT];

		if (!Validator::isIpValid($address[0]))
			throw new SteamException(self::EXCEPTION_MESSAGE_INVALID_IP, self::EXCEPTION_CODE_INVALID_IP);

		if (Validator::isDomain($address[0])) {
			$dnsRecords = dns_get_record($address[0], DNS_A);
			if (count($dnsRecords))
				$address[0] = $dnsRecords[0]['ip'];
			else
				throw new SteamException('There is no DNS entry for given domain.');
		}

		if (!Validator::isPortValid($address[1]))
			throw new SteamException(self::EXCEPTION_MESSAGE_INVALID_PORT, self::EXCEPTION_CODE_INVALID_PORT);

		return (object) [
			'ip' => $address[0],
			'port' => (int) $address[1],
		];
	}


	protected function getServersAtIp($ip, $type = MasterServer::SOURCE_MASTER_SERVER)
	{
		$master = new MasterServer($type);
		$servers = $master->getServers(MasterServer::REGION_ALL, '\\appid\\489940\\gameaddr\\' . $ip);
		return $servers;
	}
}