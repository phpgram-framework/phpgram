<?php
namespace Gram\Test\AppRouter;

use Gram\App\App;
use Gram\App\Route\MiddlewareCollector;
use Gram\Route\Interfaces\RouterInterface;
use PHPUnit\Framework\TestCase;

abstract class TestRoutes extends TestCase
{
	protected $map, $routes, $routehandler;

	/** @var MiddlewareCollector */
	protected $mwCollector;

	protected function initRoutes($method='get',$basepath ='')
	{
		App::app()->setBase($basepath);

		App::app()->group("",function () use($method){
			//init Collector
			foreach ($this->routes as $key=>$route) {
				App::app()->{$method}($route,$this->routehandler[$key])
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
		App::app()->setBase($basepath);

		App::app()->group("/group1",function () use($method){
			App::app()->{$method}("","test");
			App::app()->{$method}("/two","test2");
			App::app()->{$method}("/{id}","testid1");

			App::app()->group("/group2",function () use($method){
				App::app()->{$method}("","test3");
				App::app()->{$method}("/two","test4");
				App::app()->{$method}("/{id}","testid2");

				App::app()->group("/group3",function () use($method){
					App::app()->{$method}("","test5");
					App::app()->{$method}("/two","test6");
					App::app()->{$method}("/{id}","testid3");

					App::app()->group("/group4",function () use($method){
						App::app()->{$method}("","test7");
						App::app()->{$method}("/two","test8");
						App::app()->{$method}("/{id}","testid4");
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

	public function testRoutesWithMiddelware()
	{

		$uri = '/test/vars/123/tester';

		$router = App::app()->getRouter();

		[$status,$handler,$param] = $router->run($uri,'GET');

		$groupid=$handler[RouterInterface::ROUTE_GROUP_ID];
		$routeid=$handler[RouterInterface::ROUTE_ID];

		$mwRoute = $this->mwCollector->getRoute($routeid);
		$mwGroup = $this->mwCollector->getGroup($groupid[1]);

		self::assertEquals('Middleware 0',$mwRoute[0]);
		self::assertEquals('Middleware 2 0',$mwRoute[1]);
		self::assertEquals('Group 1',$mwGroup[0]);
		self::assertEquals('Group 1 1',$mwGroup[1]);
		self::assertEquals(200,$status);
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

		$uri = '/group1/';

		$router = App::app()->getRouter();

		[$status,$handler,$param] = $router->run($uri,'GET');

		$groupid=$handler[RouterInterface::ROUTE_GROUP_ID];

		$this->evaluateExtendedGroups($groupid);

		self::assertEquals('test',$handler[RouterInterface::ROUTE_HANDLER]);

		$uri = '/group1/21/';

		[$status,$handler,$param] = $router->run($uri,'GET');

		$groupid=$handler[RouterInterface::ROUTE_GROUP_ID];

		$this->evaluateExtendedGroups($groupid);

		self::assertEquals('testid1',$handler[RouterInterface::ROUTE_HANDLER]);
	}

	public function testExtendedGroup2()
	{

		$uri = '/group1/group2/';

		$router = App::app()->getRouter();

		[$status,$handler,$param] = $router->run($uri,'GET');

		$groupid=$handler[RouterInterface::ROUTE_GROUP_ID];

		$this->evaluateExtendedGroups($groupid);

		self::assertEquals('test3',$handler[RouterInterface::ROUTE_HANDLER]);

		$uri = '/group1/group2/21/';

		[$status,$handler,$param] = $router->run($uri,'GET');

		$groupid=$handler[RouterInterface::ROUTE_GROUP_ID];

		$this->evaluateExtendedGroups($groupid);

		self::assertEquals('testid2',$handler[RouterInterface::ROUTE_HANDLER]);
	}

	public function testExtendedGroup3()
	{

		$uri = '/group1/group2/group3/';

		$router = App::app()->getRouter();

		[$status,$handler,$param] = $router->run($uri,'GET');

		$groupid=$handler[RouterInterface::ROUTE_GROUP_ID];

		$this->evaluateExtendedGroups($groupid);

		self::assertEquals('test5',$handler[RouterInterface::ROUTE_HANDLER]);

		$uri = '/group1/group2/group3/21/';

		[$status,$handler,$param] = $router->run($uri,'GET');

		$groupid=$handler[RouterInterface::ROUTE_GROUP_ID];

		$this->evaluateExtendedGroups($groupid);

		self::assertEquals('testid3',$handler[RouterInterface::ROUTE_HANDLER]);
	}

	public function testExtendedGroup4()
	{

		$uri = '/group1/group2/group3/group4/';

		$router = App::app()->getRouter();

		[$status,$handler,$param] = $router->run($uri,'GET');

		$groupid=$handler[RouterInterface::ROUTE_GROUP_ID];

		$this->evaluateExtendedGroups($groupid);

		self::assertEquals('test7',$handler[RouterInterface::ROUTE_HANDLER]);

		$uri = '/group1/group2/group3/group4/21/';

		[$status,$handler,$param] = $router->run($uri,'GET');

		$groupid=$handler[RouterInterface::ROUTE_GROUP_ID];

		$this->evaluateExtendedGroups($groupid);

		self::assertEquals('testid4',$handler[RouterInterface::ROUTE_HANDLER]);
	}
}