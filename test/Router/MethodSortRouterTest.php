<?php
namespace Gram\Test\Router;

use Gram\Route\Collector\MiddlewareCollector;
use Gram\Route\Router;
use Nyholm\Psr7\Factory\Psr17Factory;

class MethodSortRouterTest extends TestRoutes
{
	protected function setUp(): void
	{
		$this->psr17 = new Psr17Factory();

		$this->mwCollector = new MiddlewareCollector();
		$this->router = new Router([
			'dispatcher'=>'Gram\\Route\\Dispatcher\\MethodSort\\DynamicDispatcher',
			'generator'=>'Gram\\Route\\Generator\\MethodSort\\DynamicGenerator',
		],$this->mwCollector);
		$this->collector = $this->router->getCollector();
		$this->collector->set404("404");
		$this->collector->set405("405");

		$this->map = new RouteMap();
		$this->routes = $this->map->map();
		$this->routehandler = $this->map->handler();
	}

}