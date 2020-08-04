<?php
namespace Gram\Test\AppRouter;

use Gram\App\App;

class AppRouterTest extends TestRoutes
{
	protected function setUp(): void
	{
		$this->mwCollector = App::app()->getMWCollector();

		App::app()->set404("404");
		App::app()->set405("405");

		$this->map = new RouteMap();
		$this->routes = $this->map->map();
		$this->routehandler = $this->map->handler();

		$this->initRoutes();
		$this->initExtendedGroups();
	}
}