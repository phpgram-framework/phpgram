<?php
namespace Gram\Test\Router;

use Gram\Route\Collector\RouteCollector;
use Gram\Route\Interfaces\RouterInterface;
use Gram\Route\Parser\StdParser;
use Gram\Route\Route;
use Gram\Route\RouteGroup;
use Gram\Route\Router;
use Nyholm\Psr7\Factory\Psr17Factory;
use PHPUnit\Framework\TestCase;

abstract class TestRoutes extends TestCase
{
	protected $map, $routes, $routehandler;

	/** @var Router */
	protected $router;
	/** @var RouteCollector */
	protected $collector;
	/** @var Psr17Factory */
	protected $psr17;

	/**
	 * Setze Middleware und strategy zurück, da diese als static gesammelt werden
	 * und somit über alle tests aktiv wären
	 *
	 * @throws \ReflectionException
	 */
	public static function prepareTearDown()
	{
		//setze route group zurück
		$reflectedClass = new \ReflectionClass(RouteGroup::class);
		$reflectedProperty = $reflectedClass->getProperty('middleware');
		$reflectedProperty->setAccessible(true);
		$reflectedProperty->setValue([]);

		$reflectedProperty = $reflectedClass->getProperty('strategy');
		$reflectedProperty->setAccessible(true);
		$reflectedProperty->setValue([]);
	}

	/**
	 * @throws \ReflectionException
	 */
	protected function tearDown(): void
	{
		self::prepareTearDown();
	}

	protected function initRoutes($method='get',$basepath ='')
	{
		$this->collector->setBase($basepath);

		$this->collector->group("",function () use($method){
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

	/**
	 * Nested Groups
	 *
	 * @param string $method
	 * @param string $basepath
	 */
	protected function initExtendedGroups($method='get',$basepath ='')
	{
		$this->collector->setBase($basepath);

		$this->collector->group("/group1",function (RouteCollector $r) use($method){
			$r->{$method}("","test");
			$r->{$method}("/two","test2");
			$r->{$method}("/{id}","testid1")->addStrategy("strRoute1");

			$r->group("/group2",function (RouteCollector $r) use($method){
				$r->{$method}("","test3");
				$r->{$method}("/two","test4");
				$r->{$method}("/{id}","testid2");

				$this->collector->group("/group3",function (RouteCollector $r) use($method){
					$r->{$method}("","test5");
					$r->{$method}("/two","test6");
					$r->{$method}("/{id}","testid3");

					$r->group("/group4",function (RouteCollector $r) use($method){
						$r->{$method}("","test7");
						$r->{$method}("/two","test8");
						$r->{$method}("/{id}","testid4");
					})
						->addMiddleware('mwGroup41')
						->addMiddleware('mwGroup42')
						->addStrategy('strGroup4');
				})
					->addMiddleware('mwGroup31')
					->addMiddleware('mwGroup32')
					->addStrategy('strGroup3');
			})
				->addMiddleware('mwGroup21')
				->addMiddleware('mwGroup22')
				->addStrategy('strGroup2');
		})
			->addMiddleware('mwGroup11')
			->addMiddleware('mwGroup12')
			->addStrategy('strGroup1');
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

		self::assertEquals($this->routehandler[2],$handler->handle);
		self::assertEquals(['var'=>'123@a'],$param);
	}

	public function testSimpleRoutesWithDataTyp()
	{
		$this->initRoutes();

		$uri = $this->psr17->createUri('https://jo.com/test/vars/123/tester');

		/** @var Route $handler */
		[$status,$handler,$param] = $this->router->run($uri->getPath(),'GET');

		self::assertEquals($this->routehandler[0],$handler->handle);
		self::assertEquals(['var'=>'123'],$param);
	}

	public function testSimpleRoutesWithDataTypTwo()
	{
		$this->initRoutes();

		$uri = $this->psr17->createUri('https://jo.com/test/vars/123a/tester');

		/** @var Route $handler */
		[$status,$handler,$param] = $this->router->run($uri->getPath(),'GET');

		self::assertEquals($this->routehandler[1],$handler->handle);
		self::assertEquals(['var'=>'123a'],$param);
	}

	public function testSimpleRoutesWithPost()
	{
		$this->initRoutes('post');

		$uri = $this->psr17->createUri('https://jo.com/test/vars/123a/tester');

		/** @var Route $handler */
		[$status,$handler,$param] = $this->router->run($uri->getPath(),'POST');

		self::assertEquals($this->routehandler[1],$handler->handle);
	}

	public function testSimpleRoutesWithPut()
	{
		$this->initRoutes('put');

		$uri = $this->psr17->createUri('https://jo.com/test/vars/123a/tester');

		/** @var Route $handler */
		[$status,$handler,$param] = $this->router->run($uri->getPath(),'PUT');

		self::assertEquals($this->routehandler[1],$handler->handle);
	}

	public function testSimpleRoutesWithDelete()
	{
		$this->initRoutes('delete');

		$uri = $this->psr17->createUri('https://jo.com/test/vars/123a/tester');

		/** @var Route $handler */
		[$status,$handler,$param] = $this->router->run($uri->getPath(),'DELETE');

		self::assertEquals($this->routehandler[1],$handler->handle);
	}

	public function testSimpleRoutesWithPatch()
	{
		$this->initRoutes('patch');

		$uri = $this->psr17->createUri('https://jo.com/test/vars/123a/tester');

		/** @var Route $handler */
		[$status,$handler,$param] = $this->router->run($uri->getPath(),'PATCH');

		self::assertEquals($this->routehandler[1],$handler->handle);
	}

	public function testRoutesWithMiddleware()
	{
		$this->initRoutes();

		$uri = $this->psr17->createUri('https://jo.com/test/vars/123/tester');

		/** @var Route $route */
		[$status,$route,$param] = $this->router->run($uri->getPath(),'GET');

		$mwRoute = $route->getRouteMiddleware();

		self::assertEquals('Middleware 0',$mwRoute[2]);
		self::assertEquals('Middleware 2 0',$mwRoute[3]);
		self::assertEquals('Group 1',$mwRoute[0]);
		self::assertEquals('Group 1 1',$mwRoute[1]);
	}

	public function testStartPage()
	{
		$this->initRoutes();

		$uri = $this->psr17->createUri('https://jo.com/');

		[$status,$handler,$param] = $this->router->run($uri->getPath(),'GET');

		self::assertEquals($this->routehandler[3],$handler->handle);
	}

	public function testStaticPage()
	{
		$this->initRoutes();

		$uri = $this->psr17->createUri('https://jo.com/abc/');

		[$status,$handler,$param] = $this->router->run($uri->getPath(),'GET');

		self::assertEquals($this->routehandler[4],$handler->handle);
	}

	public function testBasePath()
	{
		$this->initRoutes('GET','/base_path');

		$uri = $this->psr17->createUri('https://jo.com/base_path/abc');

		[$status,$handler,$param] = $this->router->run($uri->getPath(),'GET');

		self::assertEquals($this->routehandler[4],$handler->handle);
	}

	public function testBasePathWithFileEnding()
	{
		$this->initRoutes('GET','/base_path.php');

		$uri = $this->psr17->createUri('https://jo.com/base_path.php/abc');

		[$status,$handler,$param] = $this->router->run($uri->getPath(),'GET');

		self::assertEquals($this->routehandler[4],$handler->handle);
	}

	public function testBasePathWithStartPage()
	{
		$this->initRoutes('GET','/base_path');

		$uri = $this->psr17->createUri('https://jo.com/base_path/');

		[$status,$handler,$param] = $this->router->run($uri->getPath(),'GET');

		self::assertEquals($this->routehandler[3],$handler->handle);
	}

	public function testSimpleRoutesWithHead()
	{
		$this->initRoutes();

		$uri = $this->psr17->createUri('https://jo.com/test/vars/123a/tester');

		[$status,$handler,$param] = $this->router->run($uri->getPath(),'HEAD');

		self::assertEquals($this->routehandler[1],$handler->handle);
	}

	public function test404()
	{
		$this->initRoutes();

		$uri = $this->psr17->createUri('https://jo.com/test/vars/123a/tester1');

		[$status,$handler,$param] = $this->router->run($uri->getPath(),'GET');

		self::assertEquals('404',$status);
		self::assertEquals('404',$handler);
	}

	public function test405()
	{
		$this->initRoutes();

		$uri = $this->psr17->createUri('https://jo.com/test/vars/123a/tester');

		[$status,$handler,$param] = $this->router->run($uri->getPath(),'POST');

		self::assertEquals('405',$status);
		self::assertEquals('405',$handler);
	}

	/**
	 * Werte die Routegrups aus
	 *
	 * Hole von jeder Gruppe ihre Mw
	 *
	 * @param $groupid
	 */
	protected function evaluateExtendedGroups($groupid)
	{
		foreach ($groupid as $i=>$item) {
			if($i==0){
				continue;
			}

			$mwGroup = RouteGroup::getMiddleware($item);

			self::assertEquals("mwGroup".$i."1",$mwGroup[0]);
			self::assertEquals("mwGroup".$i."2",$mwGroup[1]);
		}
	}

	public function testExtendedGroup1()
	{
		$this->initExtendedGroups();

		$uri = $this->psr17->createUri('https://jo.com/group1/');

		/** @var Route $handler */
		[$status,$handler,$param] = $this->router->run($uri->getPath(),'GET');

		$this->evaluateExtendedGroups($handler->groupid);

		self::assertEquals('test',$handler->handle);

		$uri = $this->psr17->createUri('https://jo.com/group1/21/');

		[$status,$handler,$param] = $this->router->run($uri->getPath(),'GET');

		$this->evaluateExtendedGroups($handler->groupid);

		self::assertEquals('testid1',$handler->handle);
	}

	public function testExtendedGroup2()
	{
		$this->initExtendedGroups();

		$uri = $this->psr17->createUri('https://jo.com/group1/group2/');

		/** @var Route $handler */
		[$status,$handler,$param] = $this->router->run($uri->getPath(),'GET');

		$this->evaluateExtendedGroups($handler->groupid);

		self::assertEquals('test3',$handler->handle);

		$uri = $this->psr17->createUri('https://jo.com/group1/group2/21/');

		[$status,$handler,$param] = $this->router->run($uri->getPath(),'GET');

		$this->evaluateExtendedGroups($handler->groupid);

		self::assertEquals('testid2',$handler->handle);
	}

	public function testExtendedGroup3()
	{
		$this->initExtendedGroups();

		$uri = $this->psr17->createUri('https://jo.com/group1/group2/group3/');

		/** @var Route $handler */
		[$status,$handler,$param] = $this->router->run($uri->getPath(),'GET');

		$this->evaluateExtendedGroups($handler->groupid);

		self::assertEquals('test5',$handler->handle);

		$uri = $this->psr17->createUri('https://jo.com/group1/group2/group3/21/');

		[$status,$handler,$param] = $this->router->run($uri->getPath(),'GET');

		$this->evaluateExtendedGroups($handler->groupid);

		self::assertEquals('testid3',$handler->handle);
	}

	public function testExtendedGroup4()
	{
		$this->initExtendedGroups();

		$uri = $this->psr17->createUri('https://jo.com/group1/group2/group3/group4/');

		/** @var Route $handler */
		[$status,$handler,$param] = $this->router->run($uri->getPath(),'GET');

		$this->evaluateExtendedGroups($handler->groupid);

		self::assertEquals('test7',$handler->handle);

		$uri = $this->psr17->createUri('https://jo.com/group1/group2/group3/group4/21/');

		[$status,$handler,$param] = $this->router->run($uri->getPath(),'GET');

		$this->evaluateExtendedGroups($handler->groupid);

		self::assertEquals('testid4',$handler->handle);
	}

	public function testCustomPlaceHolders()
	{
		$this->collector->get("/test/{l:lang}/{ls:langs}","testWithPlaceholder");

		StdParser::addDataTyp('lang','de|en');
		StdParser::addDataTyp('langs','fr|sp');

		$uri = $this->psr17->createUri('https://test.de/test/de/fr/');

		/** @var Route $handler */
		[$status,$handler,$param] = $this->router->run($uri->getPath(),'GET');

		self::assertEquals(200,$status);
		self::assertEquals("testWithPlaceholder",$handler->handle);
		self::assertEquals("de",$param['l']);
		self::assertEquals("fr",$param['ls']);
	}

	public function testWithoutDynamicRoutes()
	{
		$router = new Router();
		$collector = $router->getCollector();

		$collector->get("/","no Dynamic");

		$uri = $this->psr17->createUri('https://test.de/test/de/fr/');

		[$status,$handler,$param] = $router->run($uri->getPath(),'GET');

		self::assertEquals(404,$status);
	}

	public function testWithoutAnyRoutes()
	{
		$router = new Router();

		$uri = $this->psr17->createUri('https://test.de/test/de/fr/');

		[$status,$handler,$param] = $router->run($uri->getPath(),'GET');

		self::assertEquals(404,$status);
	}

	public function testWithOptionalParam()
	{
		$this->initRoutes('GET');

		//optinal ohne Param
		$uri = $this->psr17->createUri('https://jo.com/test/optional');

		/** @var Route $handler */
		[$status,$handler,$param] = $this->router->run($uri->getPath(),'GET');

		self::assertEquals($this->routehandler[7],$handler->handle);

		//optional mit einem param
		$uri = $this->psr17->createUri('https://jo.com/test/optional/123');

		[$status,$handler,$param] = $this->router->run($uri->getPath(),'GET');

		self::assertEquals($this->routehandler[7],$handler->handle);
		self::assertEquals(123,$param["id"]);

		//optional mit zwei param
		$uri = $this->psr17->createUri('https://jo.com/test/optional/123/321');

		[$status,$handler,$param] = $this->router->run($uri->getPath(),'GET');

		self::assertEquals($this->routehandler[7],$handler->handle);
		self::assertEquals(123,$param["id"]);
		self::assertEquals(321,$param["id1"]);
	}
}