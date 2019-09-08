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
 * @author Jörn Heinemann <j.heinemann1@web.de>
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
	private $std,$group=[],$route=[];

	public function addStd($strategy)
	{
		$this->std=$strategy;
	}

	public function addRoute($routeid, $strategy)
	{
		$this->route[$routeid]=$strategy;
	}

	public function addGroup($groupid, $strategy)
	{
		$this->group[$groupid]=$strategy;
	}

	public function getStd()
	{
		return $this->std;
	}

	public function getGroup($id)
	{
		if(isset($this->group[$id])){
			return $this->group[$id];
		}
	}

	public function getRoute($id)
	{
		if(isset($this->route[$id])){
			return $this->route[$id];
		}
	}
}