<?php
/**
 * phpgram
 *
 * This File is part of the phpgram Micro Framework
 *
 * Web: https://gitlab.com/grammm/php-gram/phpgram
 *
 * @license https://gitlab.com/grammm/php-gram/phpgram/blob/master/LICENSE
 *
 * @author Jörn Heinemann <joernheinemann@gmx.de>
 */

namespace Gram\Test\App;

use Gram\Async\App\AsyncApp;
use Gram\Test\Middleware\DummyMw\TestMw1;
use Gram\Test\Middleware\DummyMw\TestMw2;
use Gram\Test\Middleware\DummyMw\TestMw3;
use Gram\Test\Router\RouteMap;
use Gram\Test\TestClasses\TestClass;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7Server\ServerRequestCreator;
use PHPUnit\Framework\TestCase;
use Pimple\Container;
use Psr\Http\Message\ServerRequestInterface;

class AsyncAppTest extends TestCase
{
	private $map, $routes, $routehandler;

	/** @var AsyncApp */
	protected $app;

	/** @var Psr17Factory */
	protected $psr17;

	/** @var ServerRequestInterface */
	protected $request;

	protected function setUp(): void
	{
		$this->app = new AsyncAppTestInit();

		$this->map = new RouteMap();
		$this->routes = $this->map->map();
		$this->routehandler = $this->map->realHandler();

		$this->app->addMiddle(new TestMw1());

		$this->initRoutes();

		$container = new Container();

		$container[TestClass::class]=function (){
			return new TestClass();
		};

		$psr11 = new \Pimple\Psr11\Container($container);

		$this->app->setContainer($psr11);

		$this->psr17 = new Psr17Factory();

		$creator = new ServerRequestCreator($this->psr17,$this->psr17,$this->psr17,$this->psr17);

		$this->request = $creator->fromGlobals();

		$this->app->building();

		$this->app->init();
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
		$uri = $this->psr17->createUri('https://jo.com/test/vars/123@/tester');

		$this->request = $this->request->withUri($uri);

		$response = $this->app->handle($this->request);

		$body = $response->getBody()->__toString();
		$status = $response->getStatusCode();

		self::assertEquals("value: 123@",$body);
		self::assertEquals(200,$status);
	}

	public function testGeneralWithDi()
	{
		$uri = $this->psr17->createUri('https://jo.com/test/vars/123a/tester');

		$this->request = $this->request->withUri($uri);

		$response = $this->app->handle($this->request);

		$body = $response->getBody()->__toString();
		$status = $response->getStatusCode();

		self::assertEquals('Gram\Test\TestClasses\TestClass right Testresult',$body);
		self::assertEquals(200,$status);
	}

	private function initWithException()
	{
		$uri = $this->psr17->createUri('https://jo.com/exception');

		$this->request = $this->request->withUri($uri);
	}

	public function testWithException()
	{
		$this->initWithException();

		$response = $this->app->handle($this->request);

		$body = $response->getBody()->__toString();
		$status = $response->getStatusCode();

		self::assertNotEquals("value: 123@",$body);
		self::assertEquals(500,$status);
	}

	public function testWithoutException()
	{
		$this->app->debugMode(1);

		$this->initWithException();

		$response = $this->app->handle($this->request);

		$body = $response->getBody()->__toString();
		$status = $response->getStatusCode();

		self::assertEquals("<h1>Application Error</h1>",$body);
		self::assertEquals(500,$status);
	}

	public function testNoException()
	{
		$this->app->debugMode(2);

		$this->initWithException();

		$response = $this->app->handle($this->request);

		$body = $response->getBody()->__toString();

		$status = $response->getStatusCode();

		self::assertEquals("",$body);
		self::assertEquals(500,$status);
	}
}