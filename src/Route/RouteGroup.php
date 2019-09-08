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

namespace Gram\Route;

use Gram\Route\Interfaces\MiddlewareCollectorInterface;
use Gram\Route\Interfaces\StrategyCollectorInterface;

/**
 * Class RouteGroup
 * @package Gram\Route
 *
 * Ein Route Group Objekt um Middleware und Strategy für die Gruppe hinzu zufügen
 */
class RouteGroup
{
	private $groupid,$stack,$strategyCollector;

	/**
	 * RouteGroup constructor.
	 * @param $prefix
	 * @param $groupid
	 * @param MiddlewareCollectorInterface $stack
	 * @param StrategyCollectorInterface $strategyCollector
	 */
	public function __construct(
		$prefix,
		$groupid,
		MiddlewareCollectorInterface $stack,
		StrategyCollectorInterface $strategyCollector
	){
		$this->groupid=$groupid;
		$this->stack=$stack;
		$this->strategyCollector=$strategyCollector;
	}

	public function addMiddleware($middleware,$order=null)
	{
		$this->stack->addGroup($this->groupid,$middleware,$order);

		return $this;
	}

	public function addStrategy($strategy)
	{
		$this->strategyCollector->addGroup($this->groupid,$strategy);

		return $this;
	}
}