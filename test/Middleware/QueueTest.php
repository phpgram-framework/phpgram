<?php
namespace Gram\Test\Middleware;

use Gram\Middleware\QueueHandler;
use Gram\Test\Middleware\DummyMw\CallableMw4;
use Gram\Test\Middleware\DummyMw\TestMw1;
use Gram\Test\Middleware\DummyMw\TestMw2;
use Gram\Test\Middleware\DummyMw\TestMw3;
use Gram\Test\Middleware\DummyMw\TestMw4Fail;
use Gram\Test\Middleware\Handler\DummyLastHandler;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7Server\ServerRequestCreator;
use PHPUnit\Framework\TestCase;
use Pimple\Container;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class QueueTest extends TestCase
{
	/** @var QueueHandler */
	private $queueHandler;

	/** @var ServerRequestInterface */
	private $request;

	private $mws=[];

	protected function setUp(): void
	{
		$this->queueHandler = new QueueHandler(new DummyLastHandler());

		$this->mws = [
			new TestMw1(),
			new TestMw2(),
			new TestMw3()
		];

		$psr17 = new Psr17Factory();

		$creator = new ServerRequestCreator($psr17,$psr17,$psr17,$psr17);

		$this->request = $creator->fromGlobals();
	}

	private function addMwNormal()
	{
		foreach ($this->mws as $mw) {
			$this->queueHandler->add($mw);
		}
	}

	/**
	 * @throws \Exception
	 */
	public function testTheQueue()
	{
		$this->addMwNormal();

		$response = $this->queueHandler->handle($this->request);

		$string = $response->getBody()->__toString();

		$expect = "Ein Stream für  123";

		self::assertEquals($expect,$string);
	}

	/**
	 * @throws \Exception
	 */
	public function testQueueWithFail()
	{
		$this->addMwNormal();

		$this->queueHandler->add(new TestMw4Fail());

		$this->expectException(\Exception::class);
		$response = $this->queueHandler->handle($this->request);

	}

	private function initContainer()
	{
		$container = new Container();

		$container['mw1']=function (){
			return new TestMw1();
		};

		$container[TestMw2::class]=function (){
			return new TestMw2();
		};

		return $container;
	}

	/**
	 * @throws \Exception
	 */
	public function testQueueWithContainer()
	{
		$psr11 = new \Pimple\Psr11\Container($this->initContainer());

		$queue = new QueueHandler(new DummyLastHandler(),$psr11);

		$queue->add('mw1');
		$queue->add(TestMw2::class);
		$queue->add(new TestMw3());

		$response = $queue->handle($this->request);

		$string = $response->getBody()->__toString();

		$expect = "Ein Stream für  123";

		self::assertEquals($expect,$string);
	}

	public function testQueueFailContainer()
	{
		$container = $this->initContainer();

		$container['Fail']=function (){
			return new TestMw4Fail();
		};

		$psr11 = new \Pimple\Psr11\Container($container);

		$queue = new QueueHandler(new DummyLastHandler(),$psr11);

		$queue->add('mw1');
		$queue->add(TestMw2::class);
		$queue->add(new TestMw3());
		$queue->add('Fail');


		try {
			$response = $queue->handle($this->request);
			$string = $response->getBody()->__toString();
		} catch (\Exception $e) {
			$string = $e->getMessage();
		}

		$expect = "Middleware needs to implement Psr 15 MiddlewareInterface or from type Callable!";

		self::assertEquals($expect,$string);
	}

	/**
	 * @throws \Exception
	 */
	public function testQueueWithCallable()
	{
		$queue = new QueueHandler(new DummyLastHandler());

		$callableMw1 = function (ServerRequestInterface $request, RequestHandlerInterface $next) {
			$words = $request->getAttribute('words',[]);

			$words[]="mw1";

			$request = $request->withAttribute('words',$words);

			return $next->handle($request);
		};

		$callableMw2 = function (ServerRequestInterface $request, RequestHandlerInterface $next) {
			$words = $request->getAttribute('words',[]);

			$words[]="mw2";

			$request = $request->withAttribute('words',$words);

			return $next->handle($request);
		};

		$queue->add($callableMw1);
		$queue->add($callableMw2);
		$queue->add(new CallableMw4());

		$response = $queue->handle($this->request);
		$string = $response->getBody()->__toString();

		$expect = "Ein Stream für callableMws mw1mw2 at the end: mw3";

		self::assertEquals($expect,$string);
	}
}