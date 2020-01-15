<?php
/**
 * phpgram
 *
 * This File is part of the phpgram Micro Framework
 *
 * Web: https://gitlab.com/grammm/php-gram/phpgram
 *
 * @license https://gitlab.com/grammm/php-gram/phpgram/blob/master/LICENSE
 *
 * @author Jörn Heinemann <joernheinemann@gmx.de>
 */

namespace Gram\Route\Collector;

use Gram\Route\Interfaces\UtilCollectorInterface;

class UtilCollector implements UtilCollectorInterface
{
	/** @var array */
	private $collect = [];

	/** @var array */
	private $route = [];

	/** @var array */
	private $group = [];

	/**
	 * @inheritdoc
	 */
	public function collect($key, $value)
	{
		$this->collect[$key][] = $value;
	}

	/**
	 * @inheritdoc
	 */
	public function collectSingle($key, $value)
	{
		$this->collect[$key] = $value;
	}

	/**
	 * @inheritdoc
	 */
	public function route($routeId, $key, $value)
	{
		$this->route[$routeId][$key][] = $value;
	}

	/**
	 * @inheritdoc
	 */
	public function routeSingle($routeId, $key, $value)
	{
		$this->route[$routeId][$key] = $value;
	}

	/**
	 * @inheritdoc
	 */
	public function group($groupId, $key, $value)
	{
		$this->group[$groupId][$key][] = $value;
	}

	/**
	 * @inheritdoc
	 */
	public function groupSingle($groupId, $key, $value)
	{
		$this->group[$groupId][$key] = $value;
	}

	/**
	 * @inheritdoc
	 */
	public function get($key)
	{
		return $this->collect[$key] ?? null;
	}

	/**
	 * @inheritdoc
	 */
	public function getRoute($routeId, $key)
	{
		return $this->route[$routeId][$key] ?? null;
	}

	/**
	 * @inheritdoc
	 */
	public function getGroup($groupId, $key)
	{
		return $this->group[$groupId][$key] ?? null;
	}
}