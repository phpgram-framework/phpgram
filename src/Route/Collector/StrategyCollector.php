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

use Gram\Route\Interfaces\StrategyCollectorInterface;

/**
 * Class StrategyCollector
 * @package Gram\Route\Collector
 *
 * Sammlet Strategy für Route und Route Groups
 *
 * ähnlich wie der @see MiddlewareCollector
 *
 * Mit dem Unterschied, dass jeweils nur eine Strategy gespeichert wird
 */
class StrategyCollector implements StrategyCollectorInterface
{
	/** @var array */
	private $group = [];

	/** @var array */
	private $route = [];

	/**
	 * @inheritdoc
	 */
	public function addRoute($routeid, $strategy)
	{
		$this->route[$routeid] = $strategy;
	}

	/**
	 * @inheritdoc
	 */
	public function addGroup($groupid, $strategy)
	{
		$this->group[$groupid] = $strategy;
	}

	/**
	 * @inheritdoc
	 */
	public function getGroup($id)
	{
		return $this->group[$id] ?? null;
	}

	/**
	 * @inheritdoc
	 */
	public function getRoute($id)
	{
		return $this->route[$id] ?? null;
	}
}