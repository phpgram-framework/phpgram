<?php
namespace Gram\Test\App;

use Gram\App\App;
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

/**
 * Class AbstractAppTest
 * @package Gram\Test\App
 *
 * Init den App Test und führe die allgemeinen Tests durch
 */
abstract class AbstractAppTest extends TestCase
{
	protected $routes, $routehandler;

	/** @var Psr17Factory */
	protected $psr17;

	/** @var ServerRequestInterface */
	protected $request;

	abstract protected function getApp():App;

	protected function initApp(): App
	{
		$app = $this->getApp();

		$map = new RouteMap();
		$this->routes = $map->map();
		$this->routehandler = $map->realHandler();

		$app->addMiddleware(new TestMw1());

		$this->initRoutes($app);

		$container = new Container();

		$container[TestClass::class]=function (){
			return new TestClass();
		};

		$psr11 = new \Pimple\Psr11\Container($container);

		$app->setContainer($psr11);

		$this->psr17 = new Psr17Factory();

		$creator = new ServerRequestCreator($this->psr17,$this->psr17,$this->psr17,$this->psr17);

		$this->request = $creator->fromGlobals();

		$app->setFactory($this->psr17);

		$app->build();

		return $app;
	}

	protected function initRoutes(App $app, $method='get',$basepath ='')
	{
		$app->setBase($basepath);

		$app->group("",function () use($app,$method){
			//init Collector
			foreach ($this->routes as $key=>$route) {
				$app->{$method}($route,$this->routehandler[$key])
					->addMiddleware(new TestMw3());
			}
		})
			->addMiddleware(new TestMw2());

	}

	public function testGeneral()
	{
		$app = $this->initApp();

		$uri = $this->psr17->createUri('https://jo.com/test/vars/123@/tester');

		$this->request = $this->request->withUri($uri);

		$response = $app->handle($this->request);

		$body = $response->getBody()->__toString();
		$status = $response->getStatusCode();

		self::assertEquals("value: 123@",$body);
		self::assertEquals(200,$status);
	}

	public function testGeneralWithDi()
	{
		$app = $this->initApp();

		$uri = $this->psr17->createUri('https://jo.com/test/vars/123a/tester');

		$this->request = $this->request->withUri($uri);

		$response = $app->handle($this->request);

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
		$app = $this->initApp();

		$this->initWithException();

		$response = $app->handle($this->request);

		$body = $response->getBody()->__toString();
		$status = $response->getStatusCode();

		self::assertNotEquals("value: 123@",$body);
		self::assertEquals(500,$status);
	}

	public function testNoException()
	{
		$app = $this->initApp();

		$app->debugMode(false);

		$this->initWithException();

		$response = $app->handle($this->request);

		$body = $response->getBody()->__toString();

		$status = $response->getStatusCode();

		self::assertEquals("",$body);
		self::assertEquals(500,$status);
	}

	public function testNotFoundException()
	{
		$app = $this->initApp();

		$uri = $this->psr17->createUri('https://jo.com/notFound');

		$this->request = $this->request->withUri($uri);

		$response = $app->handle($this->request);

		$status = $response->getStatusCode();
		self::assertEquals(404,$status);
	}

	public function testNotAllowedException()
	{
		$app = $this->initApp();

		$uri = $this->psr17->createUri('https://jo.com/exception');

		$this->request = $this->request->withUri($uri)->withMethod('POST');

		$response = $app->handle($this->request);

		$status = $response->getStatusCode();
		self::assertEquals(405,$status);
	}
}