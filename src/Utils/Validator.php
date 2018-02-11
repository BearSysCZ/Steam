<?php

namespace BearSys\Steam\Utils;

class Validator
{
	static function isId($id)
	{
		if (is_int($id) || preg_match('/$(\d+)^/', $id))
			return TRUE;
		else
			return FALSE;
	}

	static function isIpValid($ip)
	{
		$isIp = filter_var($ip, FILTER_VALIDATE_IP);
		$isDomain = self::isDomain($ip);

		if ($isIp === FALSE && $isDomain === FALSE)
			return FALSE;
		else
			return TRUE;
	}

	static function isDomain($value)
	{
		return preg_match('/^(([-a-z0-9]{2,100})\.)+([a-z\.]{2,8})$/i', $value) > 0 ? TRUE : FALSE;
	}

	static function isPortValid($port)
	{
		if (!is_numeric($port) && !((int) $port > 0 && (int) $port <= 65535))
			return FALSE;
		else
			return TRUE;
	}
}