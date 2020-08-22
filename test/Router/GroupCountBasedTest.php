<?php
namespace Gram\Test\Router;

use Gram\Route\Router;
use Nyholm\Psr7\Factory\Psr17Factory;

class GroupCountBasedTest extends TestRoutes
{
	protected function setUp(): void
	{
		$this->psr17 = new Psr17Factory();

		$this->router = new Router([
			'dispatcher'=>'Gram\\Route\\Dispatcher\\GroupCountBased',
			'generator'=>'Gram\\Route\\Generator\\GroupCountBased'
		]);
		$this->collector = $this->router->getCollector();
		$this->collector->set404("404");
		$this->collector->set405("405");

		$this->map = new RouteMap();
		$this->routes = $this->map->map();
		$this->routehandler = $this->map->handler();
	}
}