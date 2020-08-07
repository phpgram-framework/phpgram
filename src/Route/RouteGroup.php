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
	/** @var array */
	private $groupid;

	/** @var MiddlewareCollectorInterface */
	private $stack;

	/** @var StrategyCollectorInterface */
	private $strategyCollector;

	/**
	 * RouteGroup constructor.
	 * @param $groupid
	 * @param MiddlewareCollectorInterface $stack
	 * @param StrategyCollectorInterface $strategyCollector
	 */
	public function __construct(
		$groupid,
		?MiddlewareCollectorInterface $stack,
		?StrategyCollectorInterface $strategyCollector
	){
		$this->groupid = $groupid;
		$this->stack = $stack;
		$this->strategyCollector = $strategyCollector;
	}

	public function addMiddleware($middleware)
	{
		$this->stack->addGroup($this->groupid,$middleware);

		return $this;
	}

	public function addStrategy($strategy)
	{
		$this->strategyCollector->addGroup($this->groupid,$strategy);

		return $this;
	}
}