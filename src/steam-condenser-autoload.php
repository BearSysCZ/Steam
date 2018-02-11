<?php

/*
 * Removed SteamId definition because it colides with xpaw/steamid
 */

$reflection = new ReflectionClass(Composer\Autoload\ClassLoader::class);
$vendorDir = dirname(dirname($reflection->getFileName()));

define('STEAM_CONDENSER_PATH', $vendorDir . '/koraktor/steam-condenser/lib/');
define('STEAM_CONDENSER_VERSION', '1.3.10');

require_once STEAM_CONDENSER_PATH . 'steam/servers/GoldSrcServer.php';
require_once STEAM_CONDENSER_PATH . 'steam/servers/MasterServer.php';
require_once STEAM_CONDENSER_PATH . 'steam/servers/SourceServer.php';
