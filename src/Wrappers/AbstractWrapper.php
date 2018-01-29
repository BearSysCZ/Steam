<?php

namespace BearSys\Steam\Wrappers;

abstract class AbstractWrapper
{
	private $cache = [];


	/**
	 * @param string $key
	 * @param callable $setter
	 * @return mixed
	 */
	protected function getCacheKey($key, callable $setter)
	{
		if (key_exists($key, $this->cache))
			return $this->cache[$key];
		else
			return $this->cache[$key] = $setter();
	}
}