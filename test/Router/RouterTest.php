<?php
namespace Gram\Test\Router;

use Gram\Route\Collector\RouteCollector;
use Gram\Route\Interfaces\RouterInterface;
use Gram\Route\Router;
use Nyholm\Psr7\Factory\Psr17Factory;
use PHPUnit\Framework\TestCase;

class RouterTest extends TestCase
{
	private $map, $routes, $routehandler;

	/** @var Router */
	private $router;
	/** @var RouteCollector */
	private $collector;
	/** @var Psr17Factory */
	private $psr17;

	protected function setUp(): void
	{
		$this->psr17 = new Psr17Factory();

		$this->router = new Router();
		$this->collector = $this->router->getCollector();
		$this->collector->set404("404");
		$this->collector->set405("405");

		$this->map = new RouteMap();
		$this->routes = $this->map->map();
		$this->routehandler = $this->map->handler();
	}

	private function initRoutes($method='get')
	{
		//init Collector
		foreach ($this->routes as $key=>$route) {
			$this->collector->{$method}($route,$this->routehandler[$key]);
		}
	}

	public function testRouterInit()
	{
		$router = new Router();
		$collector = $router->getCollector();

		self::assertInstanceOf(RouterInterface::class,$router);
		self::assertInstanceOf(RouteCollector::class,$collector);
	}

	public function testSimpleRoutes()
	{
		$this->initRoutes();

		$uri = $this->psr17->createUri('https://jo.com/test/vars/123@a/tester');

		$this->router->run($uri->getPath(),'GET');

		$handler = $this->router->getHandle();
		$param = $this->router->getParam();

		self::assertEquals($this->routehandler[2],$handler['callable'],"Handler = ".$handler['callable']);
		self::assertEquals(['var'=>'123@a'],$param);
	}

	public function testSimpleRoutesWithDataTyp()
	{
		$this->initRoutes();

		$uri = $this->psr17->createUri('https://jo.com/test/vars/123/tester');

		$this->router->run($uri->getPath(),'GET');

		$handler = $this->router->getHandle();
		$param = $this->router->getParam();

		self::assertEquals($this->routehandler[0],$handler['callable'],"Handler = ".$handler['callable']);
		self::assertEquals(['var'=>'123'],$param);
	}

	public function testSimpleRoutesWithDataTypTwo()
	{
		$this->initRoutes();

		$uri = $this->psr17->createUri('https://jo.com/test/vars/123a/tester');

		$this->router->run($uri->getPath(),'GET');

		$handler = $this->router->getHandle();
		$param = $this->router->getParam();

		self::assertEquals($this->routehandler[1],$handler['callable'],"Handler = ".$handler['callable']);
		self::assertEquals(['var'=>'123a'],$param);
	}

	public function testSimpleRoutesWithHead()
	{
		$this->initRoutes();

		$uri = $this->psr17->createUri('https://jo.com/test/vars/123a/tester');

		$this->router->run($uri->getPath(),'HEAD');

		$handler = $this->router->getHandle();

		self::assertEquals($this->routehandler[1],$handler['callable'],"Handler = ".$handler['callable']);
	}

	public function testSimpleRoutesWithPost()
	{
		$this->initRoutes('post');

		$uri = $this->psr17->createUri('https://jo.com/test/vars/123a/tester');

		$this->router->run($uri->getPath(),'POST');

		$handler = $this->router->getHandle();

		self::assertEquals($this->routehandler[1],$handler['callable'],"Handler = ".$handler['callable']);
	}

	public function testSimpleRoutesWithPut()
	{
		$this->initRoutes('put');

		$uri = $this->psr17->createUri('https://jo.com/test/vars/123a/tester');

		$this->router->run($uri->getPath(),'PUT');

		$handler = $this->router->getHandle();

		self::assertEquals($this->routehandler[1],$handler['callable'],"Handler = ".$handler['callable']);
	}

	public function testSimpleRoutesWithDelete()
	{
		$this->initRoutes('delete');

		$uri = $this->psr17->createUri('https://jo.com/test/vars/123a/tester');

		$this->router->run($uri->getPath(),'DELETE');

		$handler = $this->router->getHandle();

		self::assertEquals($this->routehandler[1],$handler['callable'],"Handler = ".$handler['callable']);
	}

	public function testSimpleRoutesWithPatch()
	{
		$this->initRoutes('patch');

		$uri = $this->psr17->createUri('https://jo.com/test/vars/123a/tester');

		$this->router->run($uri->getPath(),'PATCH');

		$handler = $this->router->getHandle();

		self::assertEquals($this->routehandler[1],$handler['callable'],"Handler = ".$handler['callable']);
	}
}