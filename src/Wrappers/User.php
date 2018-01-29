<?php

namespace BearSys\Steam\Wrappers;

use BearSys\Steam\SteamException;
use GuzzleHttp\Exception\ClientException;
use Zyberspace\SteamWebApi\Interfaces\IPlayerService;
use Zyberspace\SteamWebApi\Interfaces\ISteamUser;
use Zyberspace\SteamWebApi\Interfaces\ISteamUserStats;

class User extends AbstractWrapper
{
	/** @var int */
	private $id;

	/** @var ISteamUser */
	private $user;

	/** @var ISteamUserStats */
	private $userStats;

	/** @var IPlayerService */
	private $playerService;

	/**
	 * @param int $id
	 * @param ISteamUser $user
	 * @param ISteamUserStats $userStats
	 * @param IPlayerService $playerService
	 */
	public function __construct($id, ISteamUser $user, ISteamUserStats $userStats, IPlayerService $playerService)
	{
		$this->id = $id;
		$this->user = $user;
		$this->userStats = $userStats;
		$this->playerService = $playerService;
	}


	public function getId()
	{
		return $this->id;
	}


	/**
	 * @return \stdClass
	 */
	public function getInfo()
	{
		return $this->getCacheKey('info', function () {
			return $this->user->GetPlayerSummariesV2($this->id)->response->players[0];
		});
	}


	/**
	 * @return string
	 */
	public function getUsername()
	{
		return $this->getInfo()->personaname;
	}


	/**
	 * @return bool
	 */
	public function isPrivate()
	{

		return $this->getInfo()->communityvisibilitystate === 3 ? FALSE : TRUE;
	}


	/**
	 * @return string
	 */
	public function getStatus()
	{
		$statuses = [
			0 => 'Offline',
			1 => 'Online',
			2 => 'Busy',
			3 => 'Away',
			4 => 'Snooze',
			5 => 'Looking to trade',
			6 => 'Looking to play'
		];

		if (!$this->isPrivate())
			return $statuses[$this->getInfo()->personastate];
		else
			return 'User data is private';
	}


	/**
	 * @return \DateTimeImmutable
	 */
	public function getLastLogoff() // TODO: maybe add check if user is online and return that, but it might lead to double return type
	{
		$datetime = new \DateTimeImmutable;
		return $datetime->setTimestamp($this->getInfo()->lastlogoff);
	}


	/**
	 * @return string
	 */
	public function getProfileUrl()
	{
		return $this->getInfo()->profileurl;
	}


	/**
	 * @param string|NULL $level NULL for 32x32, 'medium' for 64x64 or 'full' for 184x184
	 * @return string
	 * @throws SteamException
	 */
	public function getAvatar($level = NULL)
	{
		if ($level === NULL || $level === 'medium' || $level === 'full') {
			$property = 'avatar' . $level;
			return $this->getInfo()->$property;
		} else {
			throw new SteamException('Invalid avatar level provided, must be NULL, "medium" or "full"');
		}
	}


	/**
	 * @return \DateTimeImmutable
	 * @throws SteamException
	 */
	public function getRegistered()
	{
		$datetime = new \DateTimeImmutable;
		return $datetime->setTimestamp($this->getInfo()->timecreated);
	}


	/**
	 * @return string ISO code, 2 characters
	 * @throws SteamException
	 */
	public function getCountryCode()
	{
		return $this->getInfo()->loccountrycode;
	}


	/**
	 * @return \stdClass Object containing friends objects OR object with single string in case of failure
	 */
	public function getFriends()
	{
		return $this->getCacheKey('friends', function () {
			try {
				return $this->user->GetFriendListV1($this->id)->friendslist->friends;
			} catch (ClientException $e) {
				return (object) ['User data is private. Cannot display friends.'];
			}
		});
	}


	/**
	 * @return array Array of ints OR array with single string in case of failure.
	 */
	public function getGroups()
	{
		return $this->getCacheKey('groups', function () {
			$return = [];
			try {
				$groups = $this->user->GetUserGroupListV1($this->id)->response->groups;

				foreach ($groups as $group)
					$return[] = $group->gid;

				return $return;
			} catch (ClientException $e) {
				return ['User data is private. Cannot display groups.'];
			}
		});
	}


	/**
	 * @return \stdClass
	 */
	public function getBans()
	{
		return $this->getCacheKey('bans', function () {
			return $this->user->GetPlayerBansV1($this->id)->players[0];
		});
	}


	/**
	 * @return array
	 */
	public function getAchievements($gameId)
	{
		return $this->getCacheKey('achievements', function () use ($gameId) {
			$return = [];
			$achievements = $this->userStats->GetPlayerAchievementsV1($this->id, $gameId)->playerstats->achievements;
			$dateTime = new \DateTimeImmutable;

			foreach ($achievements as $achievement) {
				$return[$achievement->apiname]['achieved'] = (bool)$achievement->achieved;
				if ((bool)$achievement->achieved)
					$return[$achievement->apiname]['unlocktime'] = $dateTime->setTimestamp($achievement->unlocktime);
			}

			return $return;
		});
	}


	/**
	 * @param int $gameId
	 * @return array
	 */
	public function getGameStats($gameId)
	{
		return $this->getCacheKey('gameStats', function () use ($gameId) {
			$return = [];
			$stats = $this->userStats->GetUserStatsForGameV2($this->id, $gameId)->playerstats->stats;

			foreach ($stats as $stat)
				$return[$stat->name] = $stat->value;

			return $return;
		});
	}


	/**
	 * @return int
	 */
	public function getSteamLevel()
	{
		return $this->getCacheKey('steamLevel', function () {
			return $this->playerService->GetSteamLevelV1($this->id)->response->player_level;
		});
	}
}