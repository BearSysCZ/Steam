<?php

/*
 * Removed SteamId definition because it colides with xpaw/steamid
 */

define('STEAM_CONDENSER_PATH', dirname(__FILE__) . '/../vendor/koraktor/steam-condenser/lib/');
define('STEAM_CONDENSER_VERSION', '1.3.10');

require_once STEAM_CONDENSER_PATH . 'steam/servers/GoldSrcServer.php';
require_once STEAM_CONDENSER_PATH . 'steam/servers/MasterServer.php';
require_once STEAM_CONDENSER_PATH . 'steam/servers/SourceServer.php';
