<?php

namespace BearSys\Steam\Games;

interface IGame
{
	function retrieveData($server, $ip, $queryPort);
}
