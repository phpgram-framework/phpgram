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


	public function __construct(?string $name = null, array $data = [], string $dataName = '')
	{
		parent::__construct($name, $data, $dataName);

		$this->map = new RouteMap();
		$this->routes = $this->map->map();
		$this->routehandler = $this->map->handler();
	}

	private function initRouter()
	{
		$this->router = new Router();
		$this->collector = $this->router->getCollector();

		$this->collector->set404("404");
		$this->collector->set405("405");
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
		$this->initRouter();
		$this->initRoutes();

		$factory= new Psr17Factory();

		$uri = $factory->createUri('https://jo.com/test/vars/123@a/tester');

		$this->router->run($uri->getPath(),'GET');

		$handler = $this->router->getHandle();

		self::assertEquals($this->routehandler[2],$handler['callable'],"Handler = ".$handler['callable']);
	}

	public function testSimpleRoutesWithDataTyp()
	{
		$this->initRouter();
		$this->initRoutes();

		$factory= new Psr17Factory();

		$uri = $factory->createUri('https://jo.com/test/vars/123/tester');

		$this->router->run($uri->getPath(),'GET');

		$handler = $this->router->getHandle();

		self::assertEquals($this->routehandler[0],$handler['callable'],"Handler = ".$handler['callable']);
	}

	public function testSimpleRoutesWithDataTypTwo()
	{
		$this->initRouter();
		$this->initRoutes();

		$factory= new Psr17Factory();

		$uri = $factory->createUri('https://jo.com/test/vars/123a/tester');

		$this->router->run($uri->getPath(),'GET');

		$handler = $this->router->getHandle();

		self::assertEquals($this->routehandler[1],$handler['callable'],"Handler = ".$handler['callable']);
	}

	public function testSimpleRoutesWithHead()
	{
		$this->initRouter();
		$this->initRoutes();

		$factory= new Psr17Factory();

		$uri = $factory->createUri('https://jo.com/test/vars/123a/tester');

		$this->router->run($uri->getPath(),'HEAD');

		$handler = $this->router->getHandle();

		self::assertEquals($this->routehandler[1],$handler['callable'],"Handler = ".$handler['callable']);
	}

	public function testSimpleRoutesWithPost()
	{
		$this->initRouter();
		$this->initRoutes('post');

		$factory= new Psr17Factory();

		$uri = $factory->createUri('https://jo.com/test/vars/123a/tester');

		$this->router->run($uri->getPath(),'POST');

		$handler = $this->router->getHandle();

		self::assertEquals($this->routehandler[1],$handler['callable'],"Handler = ".$handler['callable']);
	}

	public function testSimpleRoutesWithPut()
	{
		$this->initRouter();
		$this->initRoutes('put');

		$factory= new Psr17Factory();

		$uri = $factory->createUri('https://jo.com/test/vars/123a/tester');

		$this->router->run($uri->getPath(),'PUT');

		$handler = $this->router->getHandle();

		self::assertEquals($this->routehandler[1],$handler['callable'],"Handler = ".$handler['callable']);
	}

	public function testSimpleRoutesWithDelete()
	{
		$this->initRouter();
		$this->initRoutes('delete');

		$factory= new Psr17Factory();

		$uri = $factory->createUri('https://jo.com/test/vars/123a/tester');

		$this->router->run($uri->getPath(),'DELETE');

		$handler = $this->router->getHandle();

		self::assertEquals($this->routehandler[1],$handler['callable'],"Handler = ".$handler['callable']);
	}

	public function testSimpleRoutesWithPatch()
	{
		$this->initRouter();
		$this->initRoutes('patch');

		$factory= new Psr17Factory();

		$uri = $factory->createUri('https://jo.com/test/vars/123a/tester');

		$this->router->run($uri->getPath(),'PATCH');

		$handler = $this->router->getHandle();

		self::assertEquals($this->routehandler[1],$handler['callable'],"Handler = ".$handler['callable']);
	}
}