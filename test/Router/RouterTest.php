<?php
namespace Gram\Test\Router;
use Gram\Route\Router;
use Nyholm\Psr7\Factory\Psr17Factory;
use PHPUnit\Framework\TestCase;

class RouterTest extends TestCase
{
	public function testSimpleRoutes(){
		$router= new Router();

		$collector= $router->getCollector();

		$factory= new Psr17Factory();

		$uri = $factory->createUri('https://jo.com/test/vars/123/tester');

		$routemap = new RouteMap();
		$routes = $routemap->map();
		$routehandler = $routemap->handler();

		$collector->get($routes[0],$routehandler[0]);

		$router->run($uri->getPath());

		$handler = $router->getHandle();

		self::assertEquals($routehandler[0],$handler['callable']);
	}
}