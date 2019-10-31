<?php
namespace Gram\Test\Router;

use Gram\Route\Collector\MiddlewareCollector;
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
	/** @var MiddlewareCollector */
	private $mwCollector;

	protected function setUp(): void
	{
		$this->psr17 = new Psr17Factory();

		$this->mwCollector = new MiddlewareCollector();
		$this->router = new Router([],$this->mwCollector);
		$this->collector = $this->router->getCollector();
		$this->collector->set404("404");
		$this->collector->set405("405");

		$this->map = new RouteMap();
		$this->routes = $this->map->map();
		$this->routehandler = $this->map->handler();
	}

	private function initRoutes($method='get',$basepath ='')
	{
		$this->collector->setBase($basepath);

		$this->collector->addGroup("",function () use($method){
			//init Collector
			foreach ($this->routes as $key=>$route) {
				$this->collector->{$method}($route,$this->routehandler[$key])
					->addMiddleware("Middleware $key")
					->addMiddleware("Middleware 2 $key");
			}
		})
			->addMiddleware("Group 1")
			->addMiddleware("Group 1 1");
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

		[$status,$handler,$param] = $this->router->run($uri->getPath(),'GET');


		self::assertEquals($this->routehandler[2],$handler['callable'],"Handler = ".$handler['callable']);
		self::assertEquals(['var'=>'123@a'],$param);
	}

	public function testSimpleRoutesWithDataTyp()
	{
		$this->initRoutes();

		$uri = $this->psr17->createUri('https://jo.com/test/vars/123/tester');

		[$status,$handler,$param] = $this->router->run($uri->getPath(),'GET');

		self::assertEquals($this->routehandler[0],$handler['callable'],"Handler = ".$handler['callable']);
		self::assertEquals(['var'=>'123'],$param);
	}

	public function testSimpleRoutesWithDataTypTwo()
	{
		$this->initRoutes();

		$uri = $this->psr17->createUri('https://jo.com/test/vars/123a/tester');

		[$status,$handler,$param] = $this->router->run($uri->getPath(),'GET');

		self::assertEquals($this->routehandler[1],$handler['callable'],"Handler = ".$handler['callable']);
		self::assertEquals(['var'=>'123a'],$param);
	}

	public function testSimpleRoutesWithHead()
	{
		$this->initRoutes();

		$uri = $this->psr17->createUri('https://jo.com/test/vars/123a/tester');

		[$status,$handler,$param] = $this->router->run($uri->getPath(),'HEAD');

		self::assertEquals($this->routehandler[1],$handler['callable'],"Handler = ".$handler['callable']);
	}

	public function testSimpleRoutesWithPost()
	{
		$this->initRoutes('post');

		$uri = $this->psr17->createUri('https://jo.com/test/vars/123a/tester');

		[$status,$handler,$param] = $this->router->run($uri->getPath(),'POST');

		self::assertEquals($this->routehandler[1],$handler['callable'],"Handler = ".$handler['callable']);
	}

	public function testSimpleRoutesWithPut()
	{
		$this->initRoutes('put');

		$uri = $this->psr17->createUri('https://jo.com/test/vars/123a/tester');

		[$status,$handler,$param] = $this->router->run($uri->getPath(),'PUT');

		self::assertEquals($this->routehandler[1],$handler['callable'],"Handler = ".$handler['callable']);
	}

	public function testSimpleRoutesWithDelete()
	{
		$this->initRoutes('delete');

		$uri = $this->psr17->createUri('https://jo.com/test/vars/123a/tester');

		[$status,$handler,$param] = $this->router->run($uri->getPath(),'DELETE');

		self::assertEquals($this->routehandler[1],$handler['callable'],"Handler = ".$handler['callable']);
	}

	public function testSimpleRoutesWithPatch()
	{
		$this->initRoutes('patch');

		$uri = $this->psr17->createUri('https://jo.com/test/vars/123a/tester');

		[$status,$handler,$param] = $this->router->run($uri->getPath(),'PATCH');

		self::assertEquals($this->routehandler[1],$handler['callable'],"Handler = ".$handler['callable']);
	}

	public function testRoutesWithMiddelware()
	{
		$this->initRoutes();

		$uri = $this->psr17->createUri('https://jo.com/test/vars/123/tester');

		[$status,$handler,$param] = $this->router->run($uri->getPath(),'GET');

		$groupid=$handler['groupid'];
		$routeid=$handler['routeid'];

		$mwRoute = $this->mwCollector->getRoute($routeid);
		$mwGroup = $this->mwCollector->getGroup($groupid[1]);

		self::assertEquals('Middleware 0',$mwRoute[0]);
		self::assertEquals('Middleware 2 0',$mwRoute[1]);
		self::assertEquals('Group 1',$mwGroup[0]);
		self::assertEquals('Group 1 1',$mwGroup[1]);
	}

	public function testStartPage()
	{
		$this->initRoutes();

		$uri = $this->psr17->createUri('https://jo.com/');

		[$status,$handler,$param] = $this->router->run($uri->getPath(),'GET');

		self::assertEquals($this->routehandler[3],$handler['callable'],"Handler = ".$handler['callable']);
	}

	public function testStaticPage()
	{
		$this->initRoutes();

		$uri = $this->psr17->createUri('https://jo.com/abc/');

		[$status,$handler,$param] = $this->router->run($uri->getPath(),'GET');

		self::assertEquals($this->routehandler[4],$handler['callable'],"Handler = ".$handler['callable']);
	}

	public function testBasePath()
	{
		$this->initRoutes('GET','/base_path');

		$uri = $this->psr17->createUri('https://jo.com/base_path/abc');

		[$status,$handler,$param] = $this->router->run($uri->getPath(),'GET');

		self::assertEquals($this->routehandler[4],$handler['callable']);
	}

	public function testBasePathWithStartPage()
	{
		$this->initRoutes('GET','/base_path');

		$uri = $this->psr17->createUri('https://jo.com/base_path/');

		[$status,$handler,$param] = $this->router->run($uri->getPath(),'GET');

		self::assertEquals($this->routehandler[3],$handler['callable']);
	}
}