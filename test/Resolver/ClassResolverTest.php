<?php
namespace Gram\Test\Resolver;

use Gram\Exceptions\ClassNotAllowedException;
use Gram\Exceptions\DependencyNotFoundException;
use Gram\Resolver\ClassResolver;
use Gram\Test\TestClasses\CallableClass;
use Gram\Test\TestClasses\TestClass;
use Gram\Test\TestClasses\TestClassAbstractClassForResolver;
use Gram\Test\TestClasses\TestClassDi;
use Gram\Test\TestClasses\TestClassDiDirectAccess;
use Gram\Test\TestClasses\TestClassWithDefaultDependency;
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
			//echo $e;
			return;
		}

		$resolver->setRequest($this->request);
		$resolver->setResponse($this->response);

		$this->body = $resolver->resolve($param);

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

	public function testResolveDIWithDirectAccess()
	{
		$resolver = new ClassResolver();

		$resolve = TestClassDiDirectAccess::class."@getAccess";

		$container = new Container();

		$container[TestClass::class]=function (){
			return new TestClass();
		};

		$psr11 = new \Pimple\Psr11\Container($container);

		$resolver->setContainer($psr11);

		$this->initResolve($resolver,$resolve);

		self::assertEquals(TestClass::class." right Testresult",$this->body);
	}

	public function testResolveDIWithoutDirectAccess()
	{
		$resolver = new ClassResolver();

		$resolve = TestClassDiDirectAccess::class."@getAccess";

		$this->initResolve($resolver,$resolve);

		self::assertEquals(null,$this->body);
	}

	public function testWithClassInContainer()
	{
		$resolver = new ClassResolver();

		$container = new Container();

		$resolve = TestClass::class."@doSmth";

		$container[TestClass::class]=function (){
			return new TestClass();
		};

		$psr11 = new \Pimple\Psr11\Container($container);

		$resolver->setContainer($psr11);

		$this->initResolve($resolver,$resolve);

		self::assertEquals(TestClass::class." right Testresult",$this->body);
	}

	public function testWithShortNameDependencyInContainer()
	{
		$resolver = new ClassResolver();

		$container = new Container();

		$resolve = TestClassDi::class."@testDi";

		$container['TestClass']=function (){
			return new TestClass();
		};

		$psr11 = new \Pimple\Psr11\Container($container);

		$resolver->setContainer($psr11);

		$this->initResolve($resolver,$resolve);

		self::assertEquals(TestClass::class." right Testresult",$this->body);
	}

	public function testWithDefaultDependencyInWithoutContainer()
	{
		$resolver = new ClassResolver();

		$resolve = TestClassWithDefaultDependency::class."@doing";

		$container = new Container();

		$psr11 = new \Pimple\Psr11\Container($container);

		$resolver->setContainer($psr11);

		$this->initResolve($resolver,$resolve);

		self::assertEquals(null,$this->body);
	}

	public function testWithoutRequiredDependency()
	{
		$resolver = new ClassResolver();

		$container = new Container();

		$resolve = TestClassDi::class."@testDi";

		$psr11 = new \Pimple\Psr11\Container($container);

		$resolver->setContainer($psr11);

		self::expectException(DependencyNotFoundException::class);
		$this->initResolve($resolver,$resolve);
	}

	public function testWithoutReturn()
	{
		$resolver = new ClassResolver();

		$resolve = TestClass::class."@testWithoutReturn";

		$this->initResolve($resolver,$resolve);

		self::assertEquals('',$this->body);
	}

	public function testWithoutClass()
	{
		$resolver = new ClassResolver();

		$resolve = "@testWithoutReturn";

		$this->initResolve($resolver,$resolve);

		self::assertEquals(null,$this->body);

		$resolver = new ClassResolver();

		$this->initResolve($resolver,"");

		self::assertEquals(null,$this->body);
	}

	public function testWithAbstractClass()
	{
		$resolver = new ClassResolver();

		$resolve = TestClassAbstractClassForResolver::class."@doIt";

		self::expectException(ClassNotAllowedException::class);
		$this->initResolve($resolver,$resolve);
	}

	public function testWithClassWithoutClassInterface()
	{
		$resolver = new ClassResolver();

		$resolve = CallableClass::class."@__invoke";

		self::expectException(ClassNotAllowedException::class);
		$this->initResolve($resolver,$resolve);
	}
}