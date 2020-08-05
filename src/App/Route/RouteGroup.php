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

namespace Gram\App\Route;

/**
 * Class RouteGroup
 * @package Gram\App\Route
 *
 * Eine Group bei der Middleware und Strategies hinzugefügt werden können
 */
class RouteGroup extends \Gram\Route\RouteGroup
{
	/** @var MiddlewareCollectorInterface|null */
	private $stack;

	/** @var StrategyCollectorInterface|null */
	private $strategyCollector;

	/**
	 * RouteGroup constructor.
	 * @param $groupid
	 * @param MiddlewareCollectorInterface|null $stack
	 * @param StrategyCollectorInterface|null $strategyCollector
	 */
	public function __construct(
		$groupid,
		?MiddlewareCollectorInterface $stack,
		?StrategyCollectorInterface $strategyCollector
	) {
		parent::__construct($groupid);
		$this->stack = $stack;
		$this->strategyCollector = $strategyCollector;
	}

	/**
	 * @param $middleware
	 * @return $this
	 */
	public function addMiddleware($middleware)
	{
		$this->stack->addGroup($this->groupid,$middleware);

		return $this;
	}

	/**
	 * @param $strategy
	 * @return $this
	 */
	public function addStrategy($strategy)
	{
		$this->strategyCollector->addGroup($this->groupid,$strategy);

		return $this;
	}
}