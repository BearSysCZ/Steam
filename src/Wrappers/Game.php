<?php

namespace BearSys\Steam\Wrappers;

use Zyberspace\SteamWebApi\Interfaces\ISteamNews;
use Zyberspace\SteamWebApi\Interfaces\ISteamUserStats;

class Game extends AbstractWrapper
{
	/** @var int */
	private $gameId;

	/** @var ISteamUserStats */
	private $userStats;

	/** @var ISteamNews */
	private $news;


	/**
	 * Game constructor.
	 * @param int $gameId
	 * @param ISteamUserStats $userStats
	 */
	public function __construct($gameId, ISteamUserStats $userStats, ISteamNews $news)
	{
		$this->gameId = $gameId;
		$this->userStats = $userStats;
		$this->news = $news;
	}


	/**
	 * @return mixed
	 */
	public function getAchievements()
	{
		return $this->getCacheKey('achievements', function () {
			$return = [];
			$achievements = $this->userStats->GetGlobalAchievementPercentagesForAppV2($this->gameId)->achievementpercentages->achievements;

			foreach ($achievements as $achievement)
				$return[$achievement->name] = $achievement->percent;

			return $return;
		});
	}


	/**
	 * @return mixed
	 */
	public function getActivePlayersCount()
	{
		return $this->getCacheKey('activePlayersCount', function () {
			return $this->userStats->GetNumberOfCurrentPlayersV1($this->gameId)->response->player_count;
		});
	}


	/**
	 * @param int $maxLength Maximum length for the content to return, if this is 0 the full content is returned, if it's less then a blurb is generated to fit.
	 * @param \DateTimeImmutable|NULL $endDate Retrieve posts earlier than this date
	 * @param int $count Count of posts to retrieve (default 20)
	 * @param string|NULL $feeds Comma-separated list of feed names to return news for
	 * @return mixed
	 */
	public function getNews($maxLength = 0, \DateTimeImmutable $endDate = NULL, $count = 20, $feeds = NULL)
	{
		if ($endDate instanceof \DateTimeImmutable)
			$endDate = $endDate->getTimestamp();

		return $this->news->GetNewsForAppV2($this->gameId, $maxLength, $endDate, $count, $feeds)->appnews->newsitems;
	}
}