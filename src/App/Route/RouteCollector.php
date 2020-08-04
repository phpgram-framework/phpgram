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

use Gram\Route\Interfaces\GeneratorInterface;

/**
 * Class RouteCollector
 * @package Gram\App\Route
 *
 * Ein Collector der Routes erstellt bei denen Middleware und Strategy hinzugefügt werden können
 */
class RouteCollector extends \Gram\Route\Collector\RouteCollector
{
	/** @var MiddlewareCollectorInterface|null */
	private $stack;

	/** @var StrategyCollectorInterface|null */
	private $strategyCollector;

	/**
	 * RouteCollector constructor.
	 * @param GeneratorInterface $generator
	 * @param MiddlewareCollectorInterface|null $stack
	 * @param StrategyCollectorInterface|null $strategyCollector
	 */
	public function __construct(
		GeneratorInterface $generator,
		?MiddlewareCollectorInterface $stack,
		?StrategyCollectorInterface $strategyCollector
	) {
		parent::__construct($generator);

		$this->stack = $stack;
		$this->strategyCollector = $strategyCollector;
	}

	/**
	 * Gebe eine andere Route zurück, bei der Middleware und Strategies hinzugefügt werden können
	 *
	 * @param string $path
	 * @param $handler
	 * @param $method
	 * @return Route
	 */
	protected function createRoute(string $path,$handler,$method)
	{
		return new Route(
			$path,
			$handler,
			$method,
			$this->routegroupsids,
			$this->routeid,
			$this->stack,
			$this->strategyCollector
		);
	}

	/**
	 * Erstelle eine Group die Middleware und Strategies sammeln kann
	 *
	 * @return RouteGroup
	 */
	protected function createGroup()
	{
		return new RouteGroup($this->routegroupid,$this->stack,$this->strategyCollector);
	}
}