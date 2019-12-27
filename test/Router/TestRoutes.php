<?php
namespace Gram\Test\Router;

use Gram\Route\Collector\MiddlewareCollector;
use Gram\Route\Collector\RouteCollector;
use Gram\Route\Interfaces\RouterInterface;
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
	/** @var MiddlewareCollector */
	protected $mwCollector;

	protected function initRoutes($method='get',$basepath ='')
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

	/**
	 * Nested Groups
	 *
	 * @param string $method
	 * @param string $basepath
	 */
	protected function initExtendedGroups($method='get',$basepath ='')
	{
		$this->collector->setBase($basepath);

		$this->collector->addGroup("/group1",function () use($method){
			$this->collector->{$method}("","test");
			$this->collector->{$method}("/two","test2");
			$this->collector->{$method}("/{id}","testid1");

			$this->collector->addGroup("/group2",function () use($method){
				$this->collector->{$method}("","test3");
				$this->collector->{$method}("/two","test4");
				$this->collector->{$method}("/{id}","testid2");

				$this->collector->addGroup("/group3",function () use($method){
					$this->collector->{$method}("","test5");
					$this->collector->{$method}("/two","test6");
					$this->collector->{$method}("/{id}","testid3");

					$this->collector->addGroup("/group4",function () use($method){
						$this->collector->{$method}("","test7");
						$this->collector->{$method}("/two","test8");
						$this->collector->{$method}("/{id}","testid4");
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

	public function testSimpleRoutesWithHead()
	{
		$this->initRoutes();

		$uri = $this->psr17->createUri('https://jo.com/test/vars/123a/tester');

		[$status,$handler,$param] = $this->router->run($uri->getPath(),'HEAD');

		self::assertEquals($this->routehandler[1],$handler['callable'],"Handler = ".$handler['callable']);
	}

	public function test404()
	{
		$this->initRoutes();

		$uri = $this->psr17->createUri('https://jo.com/test/vars/123a/tester1');

		[$status,$handler,$param] = $this->router->run($uri->getPath(),'GET');

		self::assertEquals('404',$status);
		self::assertEquals('404',$handler['callable']);
	}

	public function test405()
	{
		$this->initRoutes();

		$uri = $this->psr17->createUri('https://jo.com/test/vars/123a/tester');

		[$status,$handler,$param] = $this->router->run($uri->getPath(),'POST');

		self::assertEquals('405',$status);
		self::assertEquals('405',$handler['callable']);
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

			$mwGroup = $this->mwCollector->getGroup($item);

			self::assertEquals("mwGroup".$i."1",$mwGroup[0]);
			self::assertEquals("mwGroup".$i."2",$mwGroup[1]);
		}
	}

	public function testExtendedGroup1()
	{
		$this->initExtendedGroups();

		$uri = $this->psr17->createUri('https://jo.com/group1/');

		[$status,$handler,$param] = $this->router->run($uri->getPath(),'GET');

		$groupid=$handler['groupid'];

		$this->evaluateExtendedGroups($groupid);

		self::assertEquals('test',$handler['callable']);

		$uri = $this->psr17->createUri('https://jo.com/group1/21/');

		[$status,$handler,$param] = $this->router->run($uri->getPath(),'GET');

		$groupid=$handler['groupid'];

		$this->evaluateExtendedGroups($groupid);

		self::assertEquals('testid1',$handler['callable']);
	}

	public function testExtendedGroup2()
	{
		$this->initExtendedGroups();

		$uri = $this->psr17->createUri('https://jo.com/group1/group2/');

		[$status,$handler,$param] = $this->router->run($uri->getPath(),'GET');

		$groupid=$handler['groupid'];

		$this->evaluateExtendedGroups($groupid);

		self::assertEquals('test3',$handler['callable']);

		$uri = $this->psr17->createUri('https://jo.com/group1/group2/21/');

		[$status,$handler,$param] = $this->router->run($uri->getPath(),'GET');

		$groupid=$handler['groupid'];

		$this->evaluateExtendedGroups($groupid);

		self::assertEquals('testid2',$handler['callable']);
	}

	public function testExtendedGroup3()
	{
		$this->initExtendedGroups();

		$uri = $this->psr17->createUri('https://jo.com/group1/group2/group3/');

		[$status,$handler,$param] = $this->router->run($uri->getPath(),'GET');

		$groupid=$handler['groupid'];

		$this->evaluateExtendedGroups($groupid);

		self::assertEquals('test5',$handler['callable']);

		$uri = $this->psr17->createUri('https://jo.com/group1/group2/group3/21/');

		[$status,$handler,$param] = $this->router->run($uri->getPath(),'GET');

		$groupid=$handler['groupid'];

		$this->evaluateExtendedGroups($groupid);

		self::assertEquals('testid3',$handler['callable']);
	}

	public function testExtendedGroup4()
	{
		$this->initExtendedGroups();

		$uri = $this->psr17->createUri('https://jo.com/group1/group2/group3/group4/');

		[$status,$handler,$param] = $this->router->run($uri->getPath(),'GET');

		$groupid=$handler['groupid'];

		$this->evaluateExtendedGroups($groupid);

		self::assertEquals('test7',$handler['callable']);

		$uri = $this->psr17->createUri('https://jo.com/group1/group2/group3/group4/21/');

		[$status,$handler,$param] = $this->router->run($uri->getPath(),'GET');

		$groupid=$handler['groupid'];

		$this->evaluateExtendedGroups($groupid);

		self::assertEquals('testid4',$handler['callable']);
	}
}