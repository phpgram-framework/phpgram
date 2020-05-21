<?php
namespace Gram\Test\Resolver;

use Gram\Resolver\ClassResolver;
use Gram\Test\TestClasses\TestClass;
use Gram\Test\TestClasses\TestClassDi;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7Server\ServerRequestCreator;
use PHPUnit\Framework\TestCase;
use Pimple\Container;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @covers \Gram\Resolver\ClassResolver
 * @covers \Gram\Middleware\Classes\ClassTrait
 * @uses \Gram\Middleware\Classes\ClassInterface
 */
class ClassResolverTest extends TestCase
{
	/** @var ClassResolver */
	private $resolver;
	/** @var ServerRequestInterface */
	private $request;
	/** @var ResponseInterface */
	private $response;
	/** @var ResponseInterface */
	private $newResponse;

	private $body;

	protected function setUp(): void
	{
		$this->resolver = new ClassResolver();
		$factory = new Psr17Factory();
		$requestCreator = new ServerRequestCreator($factory,$factory,$factory,$factory);

		$this->request = $requestCreator->fromGlobals();
		$this->response = $factory->createResponse();
	}

	public function testInit()
	{
		$resolver = new ClassResolverTestSetter();

		$resolve = TestClass::class."@doSmth";

		try{
			$resolver->set($resolve);
		}catch (\Exception $exception){
			echo $exception;
		}

		self::assertEquals(TestClass::class,$resolver->getClassName());
		self::assertEquals("doSmth",$resolver->getFunction());
	}

	private function initResolve($resolve,$param=[])
	{
		try{
			$this->resolver->set($resolve);
		}catch (\Exception $e){
			echo $e;
		}

		$this->resolver->setRequest($this->request);
		$this->resolver->setResponse($this->response);

		try{
			$this->body = $this->resolver->resolve($param);
		}catch (\Exception $e){
			echo $e;
			$this->body=null;
		}

		$this->newResponse = $this->resolver->getResponse();
	}

	public function testResolveClass()
	{
		$resolve = TestClass::class."@doSmth";

		$this->initResolve($resolve);

		self::assertEquals(TestClass::class." right Testresult",$this->body);
	}

	public function testResolvePsrReturn()
	{
		$requestAttribut = 12;

		$this->request = $this->request->withAttribute('testCall',$requestAttribut);

		$resolve = TestClass::class."@doPsr";

		$this->initResolve($resolve);

		$newStatus = $this->newResponse->getStatusCode();

		self::assertEquals($requestAttribut,$this->body);
		self::assertEquals(404,$newStatus);
	}

	public function testWithParam()
	{
		$resolve = TestClass::class."@testWithParam";

		$param = [
			'ids'=>[1,2,3,4],
			'username'=>"Max Mustermann",
			'pw'=>'12345'
		];

		$this->initResolve($resolve,$param);

		$expect ="1 2 3 4 Max Mustermann 12345";

		self::assertEquals($expect,$this->body);
	}

	public function testResolveDI()
	{
		$resolve = TestClassDi::class."@testDi";

		$container = new Container();

		$container[TestClass::class]=function (){
			return new TestClass();
		};

		$psr11 = new \Pimple\Psr11\Container($container);

		$this->resolver->setContainer($psr11);

		$this->initResolve($resolve);

		self::assertEquals(TestClass::class." right Testresult",$this->body);
	}

	public function testWithoutReturn()
	{
		$resolve = TestClass::class."@testWithoutReturn";

		$this->initResolve($resolve);

		self::assertEquals('',$this->body);
	}
}