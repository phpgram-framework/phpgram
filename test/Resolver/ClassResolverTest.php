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

class ClassResolverTest extends TestCase
{

	/** @var ServerRequestInterface */
	private $request;
	/** @var ResponseInterface */
	private $response;
	/** @var ResponseInterface */
	private $newResponse;

	private $body;

	protected function setUp(): void
	{
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

	private function initResolve(ClassResolver $resolver,$resolve,$param=[])
	{
		try{
			$resolver->set($resolve);
		}catch (\Exception $e){
			echo $e;
		}

		$resolver->setRequest($this->request);
		$resolver->setResponse($this->response);

		try{
			$this->body = $resolver->resolve($param);
		}catch (\Exception $e){
			echo $e;
			$this->body=null;
		}

		$this->newResponse = $resolver->getResponse();
	}

	public function testResolveClass()
	{
		$resolver = new ClassResolver();

		$resolve = TestClass::class."@doSmth";

		$this->initResolve($resolver,$resolve);

		self::assertEquals(TestClass::class." right Testresult",$this->body);
	}

	public function testResolvePsrReturn()
	{
		$resolver = new ClassResolver();

		$requestAttribut = 12;

		$this->request = $this->request->withAttribute('testCall',$requestAttribut);

		$resolve = TestClass::class."@doPsr";

		$this->initResolve($resolver,$resolve);

		$newStatus = $this->newResponse->getStatusCode();

		self::assertEquals($requestAttribut,$this->body);
		self::assertEquals(404,$newStatus);
	}

	public function testWithParam()
	{
		$resolver = new ClassResolver();

		$resolve = TestClass::class."@testWithParam";

		$param = [
			'ids'=>[1,2,3,4],
			'username'=>"Max Mustermann",
			'pw'=>'12345'
		];

		$this->initResolve($resolver,$resolve,$param);

		$expect ="1 2 3 4 Max Mustermann 12345";

		self::assertEquals($expect,$this->body);
	}

	public function testResolveDI()
	{
		$resolver = new ClassResolver();

		$resolve = TestClassDi::class."@testDi";

		$container = new Container();

		$container[TestClass::class]=function (){
			return new TestClass();
		};

		$psr11 = new \Pimple\Psr11\Container($container);

		$resolver->setContainer($psr11);

		$this->initResolve($resolver,$resolve);

		self::assertEquals(TestClass::class." right Testresult",$this->body);
	}

	public function testWithoutReturn()
	{
		$resolver = new ClassResolver();

		$resolve = TestClass::class."@testWithoutReturn";

		$this->initResolve($resolver,$resolve);

		self::assertEquals('',$this->body);
	}
}