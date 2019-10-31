<?php
namespace Gram\Test\App;

use Gram\App\App;
use Gram\Test\Middleware\DummyMw\TestMw1;
use Gram\Test\Middleware\DummyMw\TestMw2;
use Gram\Test\Middleware\DummyMw\TestMw3;
use Gram\Test\Router\RouteMap;
use PHPUnit\Framework\TestCase;

class AppTest extends TestCase
{
	private $map, $routes, $routehandler;

	/** @var App */
	protected $app;

	protected function setUp(): void
	{
		$this->app = new AppTestInit();

		$this->map = new RouteMap();
		$this->routes = $this->map->map();

		$this->app->addMiddle(new TestMw1());

		$this->initRoutes();

		$this->app->build();
	}

	private function initRoutes($method='get',$basepath ='')
	{
		$this->app->setBase($basepath);

		$this->app->addGroup("",function () use($method){
			//init Collector
			foreach ($this->routes as $key=>$route) {
				$this->app->{$method}($route,$this->routehandler[$key])
					->addMiddleware(new TestMw3());
			}
		})
			->addMiddleware(new TestMw2());

	}

	public function testGeneral()
	{

	}
}