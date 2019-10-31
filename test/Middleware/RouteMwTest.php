<?php
namespace Gram\Test\Middleware;

use Gram\App\QueueHandler;
use Gram\Middleware\Handler\NotFoundHandler;
use Gram\Middleware\RouteMiddleware;
use Gram\Route\Collector\MiddlewareCollector;
use Gram\Route\Collector\RouteCollector;
use Gram\Route\Collector\StrategyCollector;
use Gram\Route\Router;
use Gram\Test\App\AppTestInit;
use Gram\Test\Middleware\DummyMw\TestMw1;
use Gram\Test\Middleware\DummyMw\TestMw2;
use Gram\Test\Middleware\DummyMw\TestMw3;
use Gram\Test\Middleware\Handler\DummyLastHandler;
use Gram\Test\Router\RouteMap;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7Server\ServerRequestCreator;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;

class RouteMwTest extends TestCase
{
	private $router, $notFundHandler, $lastHandler, $mwCollector, $strategyCollector, $map;

	/** @var Psr17Factory */
	private $psr17;
	/** @var ServerRequestInterface */
	private $request;
	/** @var RouteMiddleware */
	private $routemw;
	/** @var QueueHandler */
	private $queue;
	/** @var RouteCollector */
	private $routeCollector;

	protected function setUp(): void
	{
		$app = new AppTestInit();

		$this->mwCollector = $app->getMWCollector();
		$this->strategyCollector = $app->getStrategyCollector();

		$this->router = new Router(
			[],
			$this->mwCollector,
			$this->strategyCollector
		);
		$this->routeCollector = $this->router->getCollector();

		$this->lastHandler = new DummyLastHandler();
		$this->notFundHandler = new NotFoundHandler($this->lastHandler);
		$this->queue = new QueueHandler($this->lastHandler);

		$this->map = new RouteMap();
		$routes = $this->map->map();
		$routehandler = $this->map->handler();

		$method = 'GET';

		$this->routeCollector->addGroup("",function () use($method,$routehandler,$routes){
			//init Collector
			foreach ($routes as $key=>$route) {
				$this->routeCollector->any($route,$routehandler[$key])
					->addMiddleware(new TestMw3());
			}
		})
			->addMiddleware(new TestMw2());

		$this->mwCollector->addStd(new TestMw1());

		$this->routemw = new RouteMiddleware(
			$this->router,
			$this->notFundHandler,
			$app,
			$this->strategyCollector
		);

		$app->setQueueHandler($this->queue);

		$app->setRouteMiddleware($this->routemw);

		$app->building();

		$app->init();

		$this->psr17 = new Psr17Factory();

		$creator = new ServerRequestCreator($this->psr17,$this->psr17,$this->psr17,$this->psr17);

		$this->request = $creator->fromGlobals();
	}

	public function testRouterFound()
	{
		$uri = $this->psr17->createUri('https://jo.com/test/vars/123@/tester');

		$this->request = $this->request->withUri($uri);

		$response = $this->queue->handle($this->request);

		$status = $response->getStatusCode();

		self::assertEquals(200,$status);

		$string = $response->getBody()->__toString();

		$expect = "Ein Stream für TestHandler2 123";

		self::assertEquals($expect,$string);
	}

	public function testNotFound()
	{
		$uri = $this->psr17->createUri('https://jo.com/test/vars/123@/tester1');

		$this->request = $this->request->withUri($uri);

		$this->routeCollector->set404('Not Found');

		$response = $this->queue->handle($this->request);

		$status = $response->getStatusCode();

		self::assertEquals(404,$status);

		$string = $response->getBody()->__toString();

		$expect = "Ein Stream für Not Found 1";

		self::assertEquals($expect,$string);
	}
}