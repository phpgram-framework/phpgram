<?php
namespace Gram\Test\Router;

use Gram\Route\Collector\MiddlewareCollector;
use Gram\Route\Router;
use Nyholm\Psr7\Factory\Psr17Factory;

/**
 * @covers \Gram\Route\Router
 * @covers \Gram\Route\Route
 * @covers \Gram\Route\Collector\RouteCollector
 * @covers \Gram\Route\Dispatcher\Dispatcher
 * @covers \Gram\Route\Collector\MiddlewareCollector
 * @covers \Gram\Route\Collector\StrategyCollector
 * @covers \Gram\Route\Generator\GroupCountBased
 * @covers \Gram\Route\Dispatcher\GroupCountBased
 */
class GroupCountBasedTest extends TestRoutes
{
	protected function setUp(): void
	{
		$this->psr17 = new Psr17Factory();

		$this->mwCollector = new MiddlewareCollector();
		$this->router = new Router([
			'dispatcher'=>'Gram\\Route\\Dispatcher\\GroupCountBased',
			'generator'=>'Gram\\Route\\Generator\\GroupCountBased'
		],$this->mwCollector);
		$this->collector = $this->router->getCollector();
		$this->collector->set404("404");
		$this->collector->set405("405");

		$this->map = new RouteMap();
		$this->routes = $this->map->map();
		$this->routehandler = $this->map->handler();
	}
}