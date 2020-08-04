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
 * Class Route
 * @package Gram\App\Route
 *
 * Eine Route bei der Middleware und Strategies hinzugefügt werden können
 */
class Route extends \Gram\Route\Route
{
	/**
	 * @var MiddlewareCollectorInterface|null
	 */
	private $stack;
	/**
	 * @var StrategyCollectorInterface|null
	 */
	private $strategyCollector;

	/**
	 * Route constructor.
	 * @param string $path
	 * @param $handle
	 * @param $method
	 * @param $routegroupid
	 * @param $routeid
	 * @param MiddlewareCollectorInterface|null $stack
	 * @param StrategyCollectorInterface|null $strategyCollector
	 */
	public function __construct(
		string $path, $handle, $method, $routegroupid, $routeid,
		?MiddlewareCollectorInterface $stack = null,
		?StrategyCollectorInterface $strategyCollector = null
	) {
		parent::__construct($path, $handle, $method, $routegroupid, $routeid);

		$this->stack = $stack;
		$this->strategyCollector = $strategyCollector;
	}

	/**
	 * Kann nach dem definieren einer Route aufgerufen werden um mehre Middleware hinzu zufügen
	 *
	 * @param $middleware
	 * @return $this
	 */
	public function addMiddleware($middleware)
	{
		$this->stack->addRoute($this->routeid,$middleware);

		return $this;
	}

	public function addStrategy($strategy)
	{
		$this->strategyCollector->addRoute($this->routeid,$strategy);

		return $this;
	}
}