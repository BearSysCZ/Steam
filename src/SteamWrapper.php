<?php

namespace BearSys\Steam;

use BearSys\Steam\Wrappers\Api;
use BearSys\Steam\Wrappers\Game;
use Nette\Object;
use Nette\Utils\Validators;

class SteamWrapper extends Object
{
	const FORMAT_STEAM2 = 'steam2';
	const FORMAT_STEAM3 = 'steam3';
	const FORMAT_64BIT = '64bit';
	const FORMAT_URL = 'url';

	private $apiKey;
	private $api;


	/**
	 * SteamWrapper constructor.
	 * @param string $apiKey
	 */
	public function __construct($apiKey)
	{
		Validators::assert($apiKey, 'string:32', 'Steam Web API key - string, exactly 32 characters');
		$this->apiKey = $apiKey; // TODO: check if key is correct
		$this->api = new Api($apiKey, $this);
	}


	/**
	 * @param string $id Steam ID in any format or profile url
	 * @return Wrappers\User
	 */
	public function getUser($id)
	{
		return $this->api->getUser($id);
	}


	/**
	 * @param int $id
	 * @return Game
	 */
	public function getGame($id)
	{
		return $this->api->getGame($id);
	}


	/**
	 * @param string $src Steam ID in any format or profile url that you wish to convert
	 * @param string $format Target format, should be SteamWrapper::FORMAT_*
	 * @return string
	 * @throws SteamException
	 */
	public function convertSteamId($src, $format)
	{
		try {
			$steamId = \SteamID::SetFromURL($src, function ($url, $type) {
				$response = $this->api->resolveVanityName($url, $type);

				if (isset($response->response->success)) {
					switch ((int) $response->response->success) {
						case 1: return $response->response->steamid;
						case 42: return null;
					}
				}

				throw new \Exception( 'Failed to perform API request' );
			});

			if ($steamId->IsValid())
				switch ($format) {
					case self::FORMAT_STEAM2:
						return $steamId->RenderSteam2();
					case self::FORMAT_STEAM3:
						return $steamId->RenderSteam3();
					case self::FORMAT_64BIT:
						return $steamId->ConvertToUInt64();
					case self::FORMAT_URL:
						return $this->getUser($steamId->ConvertToUInt64())->getInfo()->profileurl;
					default:
						throw new SteamException('Unsupported format of Steam ID. Check BearSys\\NetteSteam\\SteamWrapper for valid formats.');
				}
			else
				throw new SteamException('Given Steam ID is invalid!');
		} catch (\Exception $e) {
			throw new SteamException('Steam ID parsing error: ' . $e->getMessage());
		}
	}
}