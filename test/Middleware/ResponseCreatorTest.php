<?php
namespace Gram\Test\Middleware;

use Gram\Middleware\ResponseCreator;
use Gram\ResolverCreator\ResolverCreator;
use Gram\Strategy\BufferAppStrategy;
use Gram\Strategy\JsonStrategy;
use Gram\Strategy\StdAppStrategy;
use Gram\Test\TestClasses\ControllerTestClass;
use Gram\Test\TestClasses\TestClass;
use Gram\Test\TestClasses\TestClassDi;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7Server\ServerRequestCreator;
use PHPUnit\Framework\TestCase;
use Pimple\Container;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ResponseCreatorTest extends TestCase
{

	/** @var RequestHandlerInterface */
	protected $responseCreator;

	/** @var Psr17Factory */
	protected $psr17;

	/** @var ServerRequestInterface */
	protected $request;

	/** @var ContainerInterface */
	protected $container;

	protected function setUp(): void
	{
		$this->psr17 = new Psr17Factory();
		$creator = new ServerRequestCreator($this->psr17,$this->psr17,$this->psr17,$this->psr17);

		$this->request = $creator->fromGlobals();

		$resolveCreator = new ResolverCreator();

		$strategy = new StdAppStrategy();

		$container = new Container();

		$container[TestClass::class]=function (){
			return new TestClass();
		};

		$container[BufferAppStrategy::class] = function () {
			return new BufferAppStrategy();
		};

		$container[JsonStrategy::class] = function () {
			return new JsonStrategy();
		};

		$this->container = new \Pimple\Psr11\Container($container);

		$this->responseCreator = new ResponseCreator(
			$this->psr17,
			$resolveCreator,
			$strategy,
			$this->container
		);
	}

	/**
	 * @throws \Gram\Exceptions\CallableNotFoundException
	 * @throws \Gram\Exceptions\StrategyNotAllowedException
	 */
	public function testCreateResponseCreator()
	{
		$responseCreator = new ResponseCreator(
			$this->psr17,
			new ResolverCreator(),
			new StdAppStrategy(),
			$this->container
		);

		$this->request = $this->request->withAttribute('callable',TestClassDi::class."@testDi")
			->withAttribute('status',200);

		$response = $responseCreator->handle($this->request);

		self::assertEquals(true, $responseCreator instanceof ResponseCreator);

		$body = $response->getBody()->__toString();
		$head = $response->getHeader('Content-Type');

		self::assertEquals('text/html',$head[0]);
		self::assertEquals('Gram\Test\TestClasses\TestClass right Testresult',$body);
	}

	public function testSimple()
	{
		$this->request = $this->request->withAttribute('callable',TestClassDi::class."@testDi")
			->withAttribute('status',200);

		$response = $this->responseCreator->handle($this->request);

		$body = $response->getBody()->__toString();
		$head = $response->getHeader('Content-Type');

		self::assertEquals('text/html',$head[0]);
		self::assertEquals('Gram\Test\TestClasses\TestClass right Testresult',$body);
	}

	public function testWithBufferStrategy()
	{
		$this->request = $this->request->withAttribute('callable',ControllerTestClass::class."@buffered")
			->withAttribute('status',200)
			->withAttribute('strategy',new BufferAppStrategy());

		$response = $this->responseCreator->handle($this->request);

		$body = $response->getBody()->__toString();
		$head = $response->getHeader('Content-Type');

		self::assertEquals('text/html',$head[0]);
		self::assertEquals("buffer Test",$body);
	}

	public function testWithBufferStrategyAsString()
	{
		$this->request = $this->request->withAttribute('callable',ControllerTestClass::class."@buffered")
			->withAttribute('status',200)
			->withAttribute('strategy',BufferAppStrategy::class);

		$response = $this->responseCreator->handle($this->request);

		$body = $response->getBody()->__toString();
		$head = $response->getHeader('Content-Type');

		self::assertEquals('text/html',$head[0]);
		self::assertEquals("buffer Test",$body);
	}

	public function testWithJsonStrategy()
	{
		$this->request = $this->request->withAttribute('callable',ControllerTestClass::class."@json")
			->withAttribute('status',200)
			->withAttribute('strategy',new JsonStrategy());

		$response = $this->responseCreator->handle($this->request);

		$body = $response->getBody()->__toString();
		$head = $response->getHeader('Content-Type');

		self::assertEquals('application/json',$head[0]);
		self::assertEquals('["value1","value2","value3"]',$body);
	}

	public function testWithJsonStrategyAsString()
	{
		$this->request = $this->request->withAttribute('callable',ControllerTestClass::class."@json")
			->withAttribute('status',200)
			->withAttribute('strategy',JsonStrategy::class);

		$response = $this->responseCreator->handle($this->request);

		$body = $response->getBody()->__toString();
		$head = $response->getHeader('Content-Type');

		self::assertEquals('application/json',$head[0]);
		self::assertEquals('["value1","value2","value3"]',$body);
	}

	public function testWithFunction()
	{
		$func = function () {
			return "test_func";
		};

		$this->request = $this->request->withAttribute('callable',$func)
			->withAttribute('status',200);

		$response = $this->responseCreator->handle($this->request);

		$body = $response->getBody()->__toString();

		self::assertEquals("test_func",$body);
	}

	public function testIfCallableReturnsResponseBuffered()
	{
		$this->request = $this->request->withAttribute('callable',ControllerTestClass::class."@returnResponse")
			->withAttribute('status',200)
			->withAttribute('strategy',BufferAppStrategy::class);

		$response = $this->responseCreator->handle($this->request);

		$body = $response->getBody()->__toString();
		$head = $response->getHeader('Content-Type');

		self::assertEquals('text/html',$head[0]);
		self::assertEquals("hello",$body);
	}
}