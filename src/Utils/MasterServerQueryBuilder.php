<?php

namespace BearSys\Steam\Utils;

use BearSys\Steam\SteamException;
use BearSys\Steam\SteamServers;

class MasterServerQueryBuilder
{
	const OPTION_APP_ID = 'appId';
	const OPTION_IP = 'ip';
	const OPTION_PRIVATE = 'private';
	const OPTION_EMPTY = 'empty';
	const OPTION_FULL = 'full';


	private $query = '';


	public function setAppId($id)
	{
		if (!Validator::isId($id))
			throw new SteamException('Invalid app ID: ' . $id . '. Has to be integer.');

		$this->addToQuery('appid', (int) $id);
	}


	public function setIp($ip)
	{
		if (!Validator::isIpValid($ip))
			throw new SteamException(SteamServers::EXCEPTION_MESSAGE_INVALID_IP, SteamServers::EXCEPTION_CODE_INVALID_IP);

		if (Validator::isDomain($ip)) {
			$dnsRecords = dns_get_record($ip, DNS_A);
			if (count($dnsRecords))
				$ip = $dnsRecords[0]['ip'];
			else
				throw new SteamException('There is no DNS entry for given domain.');
		}

		$this->addToQuery('gameaddr', $ip);
	}


	public function setPrivate($private)
	{
		if (!is_bool($private))
			throw new \Exception('Bool required when setting private, ' . gettype($private) . ' given.');

		$this->addToQuery('password', $private ? 1 : 0);
	}


	public function setEmpty($empty)
	{
		if (!is_bool($empty))
			throw new \Exception('Bool required when setting empty, ' . gettype($empty) . ' given.');

		if ($empty)
			$this->addToQuery('noplayers', 1);
		else
			$this->addToQuery('empty', 1);
	}


	public function setFull($full)
	{
		if (!is_bool($full))
			throw new \Exception('Bool required when setting full, ' . gettype($full) . ' given.');

		if ($full)
			$this->addToQuery('full', 0);
		else
			$this->addToQuery('full', 1);
	}


	public function getQuery()
	{
		return $this->query;
	}


	protected function addToQuery($filter, $value)
	{
		$this->query .= '\\' . $filter . '\\' . $value;
	}
}