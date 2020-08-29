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

class ResponseCreatorTest extends TestCase
{

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
	}

	private function getResponseCreator()
	{
		return new ResponseCreator(
			$this->psr17,
			new ResolverCreator(),
			new StdAppStrategy(),
			$this->container
		);
	}

	/**
	 * @throws \Gram\Exceptions\CallableNotFoundException
	 * @throws \Gram\Exceptions\StrategyNotAllowedException
	 */
	public function testCreateResponseCreator()
	{
		$responseCreator = $this->getResponseCreator();

		$request = $this->request->withAttribute('callable',TestClassDi::class."@testDi")
			->withAttribute('status',200);

		$response = $responseCreator->handle($request);

		self::assertEquals(true, $responseCreator instanceof ResponseCreator);

		$body = $response->getBody()->__toString();
		$head = $response->getHeader('Content-Type');

		self::assertEquals('text/html',$head[0]);
		self::assertEquals('Gram\Test\TestClasses\TestClass right Testresult',$body);
	}

	/**
	 * @throws \Gram\Exceptions\CallableNotFoundException
	 * @throws \Gram\Exceptions\StrategyNotAllowedException
	 */
	public function testSimple()
	{
		$responseCreator = $this->getResponseCreator();

		$request = $this->request->withAttribute('callable',TestClassDi::class."@testDi")
			->withAttribute('status',200);

		$response = $responseCreator->handle($request);

		$body = $response->getBody()->__toString();
		$head = $response->getHeader('Content-Type');

		self::assertEquals('text/html',$head[0]);
		self::assertEquals('Gram\Test\TestClasses\TestClass right Testresult',$body);
	}

	/**
	 * @throws \Gram\Exceptions\CallableNotFoundException
	 * @throws \Gram\Exceptions\StrategyNotAllowedException
	 */
	public function testWithResponseReturned()
	{
		$responseCreator = $this->getResponseCreator();

		$request = $this->request->withAttribute('callable',ControllerTestClass::class."@returnResponse")
			->withAttribute('status',200);

		$response = $responseCreator->handle($request);

		$body = $response->getBody()->__toString();
		$head = $response->getHeader('Content-Type');

		self::assertEquals('text/html',$head[0]);
		self::assertEquals('hello',$body);
	}

	/**
	 * @throws \Gram\Exceptions\CallableNotFoundException
	 * @throws \Gram\Exceptions\StrategyNotAllowedException
	 */
	public function testWithBufferStrategy()
	{
		$responseCreator = $this->getResponseCreator();

		$request = $this->request->withAttribute('callable',ControllerTestClass::class."@buffered")
			->withAttribute('status',200)
			->withAttribute('strategy',new BufferAppStrategy());

		$response = $responseCreator->handle($request);

		$body = $response->getBody()->__toString();
		$head = $response->getHeader('Content-Type');

		self::assertEquals('text/html',$head[0]);
		self::assertEquals("buffer Test",$body);
	}

	/**
	 * @throws \Gram\Exceptions\CallableNotFoundException
	 * @throws \Gram\Exceptions\StrategyNotAllowedException
	 */
	public function testWithBufferStrategyAsString()
	{
		$responseCreator = $this->getResponseCreator();

		$request = $this->request->withAttribute('callable',ControllerTestClass::class."@buffered")
			->withAttribute('status',200)
			->withAttribute('strategy',BufferAppStrategy::class);

		$response = $responseCreator->handle($request);

		$body = $response->getBody()->__toString();
		$head = $response->getHeader('Content-Type');

		self::assertEquals('text/html',$head[0]);
		self::assertEquals("buffer Test",$body);
	}

	/**
	 * @throws \Gram\Exceptions\CallableNotFoundException
	 * @throws \Gram\Exceptions\StrategyNotAllowedException
	 */
	public function testWithJsonStrategy()
	{
		$responseCreator = $this->getResponseCreator();

		$request = $this->request->withAttribute('callable',ControllerTestClass::class."@json")
			->withAttribute('status',200)
			->withAttribute('strategy',new JsonStrategy());

		$response = $responseCreator->handle($request);

		$body = $response->getBody()->__toString();
		$head = $response->getHeader('Content-Type');

		self::assertEquals('application/json',$head[0]);
		self::assertEquals('["value1","value2","value3"]',$body);
	}

	/**
	 * @throws \Gram\Exceptions\CallableNotFoundException
	 * @throws \Gram\Exceptions\StrategyNotAllowedException
	 */
	public function testWithJsonStrategyAsString()
	{
		$responseCreator = $this->getResponseCreator();

		$request = $this->request->withAttribute('callable',ControllerTestClass::class."@json")
			->withAttribute('status',200)
			->withAttribute('strategy',JsonStrategy::class);

		$response = $responseCreator->handle($request);

		$body = $response->getBody()->__toString();
		$head = $response->getHeader('Content-Type');

		self::assertEquals('application/json',$head[0]);
		self::assertEquals('["value1","value2","value3"]',$body);
	}

	/**
	 * @throws \Gram\Exceptions\CallableNotFoundException
	 * @throws \Gram\Exceptions\StrategyNotAllowedException
	 */
	public function testWithFunction()
	{
		$responseCreator = $this->getResponseCreator();

		$func = function () {
			return "test_func";
		};

		$request = $this->request->withAttribute('callable',$func)
			->withAttribute('status',200);

		$response = $responseCreator->handle($request);

		$body = $response->getBody()->__toString();

		self::assertEquals("test_func",$body);
	}

	/**
	 * @throws \Gram\Exceptions\CallableNotFoundException
	 * @throws \Gram\Exceptions\StrategyNotAllowedException
	 */
	public function testIfCallableReturnsResponseBuffered()
	{
		$responseCreator = $this->getResponseCreator();

		$request = $this->request->withAttribute('callable',ControllerTestClass::class."@returnResponse")
			->withAttribute('status',200)
			->withAttribute('strategy',BufferAppStrategy::class);

		$response = $responseCreator->handle($request);

		$body = $response->getBody()->__toString();
		$head = $response->getHeader('Content-Type');

		self::assertEquals('text/html',$head[0]);
		self::assertEquals("hello",$body);
	}
}