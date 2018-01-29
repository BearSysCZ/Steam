<?php

namespace BearSys\Steam\Wrappers;

use BearSys\Steam\SteamException;
use BearSys\Steam\SteamWrapper;
use Zyberspace\SteamWebApi\AbstractInterface;
use Zyberspace\SteamWebApi\Client;
use Zyberspace\SteamWebApi\Interfaces\IPlayerService;
use Zyberspace\SteamWebApi\Interfaces\ISteamNews;
use Zyberspace\SteamWebApi\Interfaces\ISteamUser;
use Zyberspace\SteamWebApi\Interfaces\ISteamUserStats;

class Api
{
	/** @var Client */
	private $client;

	/** @var SteamWrapper */
	private $wrapper;

	/** @var array */
	private $interfaces = [];

	/** @var array */
	private $cache = [
		'users' => [],
		'games' => [],
	];


	public function __construct($apiKey, SteamWrapper $wrapper)
	{
		$this->client = new Client($apiKey);
		$this->wrapper = $wrapper;
	}


	/**
	 * @param string $id Any form of ID or url
	 * @return User
	 */
	public function getUser($id)
	{
		$parsedId = $this->wrapper->convertSteamId($id, SteamWrapper::FORMAT_64BIT);

		if (key_exists($parsedId, $this->cache['users']) && $this->cache['users'][$parsedId] instanceof User)
			return $this->cache['users'][$parsedId];
		else
			return $this->cache['users'][$parsedId] = new User($parsedId, $this->getInterface(ISteamUser::class), $this->getInterface(ISteamUserStats::class), $this->getInterface(IPlayerService::class));
	}


	/**
	 * @param int $id
	 * @return Game
	 */
	public function getGame($id)
	{
		if (key_exists($id, $this->cache['games']) && $this->cache['games'][$id] instanceof Game)
			return $this->cache['games'][$id];
		else
			return $this->cache['games'][$id] = new Game($id, $this->getInterface(ISteamUserStats::class), $this->getInterface(ISteamNews::class));
	}


	public function resolveVanityName($url, $type)
	{
		return $this->getInterface(ISteamUser::class)->ResolveVanityURLV1($url, $type);
	}


	/**
	 * @param string $name Steam API interface name. It must implement Zyberspace\SteamWebApi\AbstractInterface.
	 * @return AbstractInterface
	 * @throws SteamException
	 */
	private function getInterface($name)
	{
		if (!key_exists($name, $this->interfaces) || !$this->interfaces[$name] instanceof AbstractInterface) {
			if (!class_exists($name))
				throw new SteamException('Interface ' . $name . ' does not exist.');

			$class = new $name($this->client);

			if (!$class instanceof AbstractInterface)
				throw new SteamException('Interface ' . $name . ' must implement \\Zyberspace\\SteamWebApi\\AbstractInterface.');

			$this->interfaces[$name] = $class;
		}

		return $this->interfaces[$name];
	}
}