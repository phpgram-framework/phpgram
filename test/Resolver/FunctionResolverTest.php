<?php
namespace Gram\Test\Resolver;

use Gram\Resolver\ClosureResolver;
use Gram\Test\TestClasses\TestClass;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7Server\ServerRequestCreator;
use PHPUnit\Framework\TestCase;
use Pimple\Container;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class FunctionResolverTest extends TestCase
{
	/** @var ClosureResolver */
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
		$this->resolver = new ClosureResolver();
		$factory = new Psr17Factory();
		$requestCreator = new ServerRequestCreator($factory,$factory,$factory,$factory);

		$this->request = $requestCreator->fromGlobals();
		$this->response = $factory->createResponse();
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

	public function testFunctions()
	{
		$function = function (){
			return "Test erfolgreich";
		};

		$this->initResolve($function);

		self::assertEquals("Test erfolgreich",$this->body);
	}

	public function testFuctionsWithParam()
	{
		$function = function ($param1,$param2,$param3){
			$string1= implode(" ",$param1);

			return "$string1 $param2 $param3";
		};

		$param = [
			'ids'=>[1,2,3,4],
			'username'=>"Max Mustermann",
			'pw'=>'12345'
		];

		$this->initResolve($function,$param);

		$expect ="1 2 3 4 Max Mustermann 12345";

		self::assertEquals($expect,$this->body);
	}

	public function testFunctionPsr()
	{
		$requestAttribut = 12;

		$this->request = $this->request->withAttribute('testCall',$requestAttribut);
		$this->response=$this->response->withStatus(200);

		$function = function (){
			$this->response=$this->response->withStatus(404);

			$callTest = $this->request->getAttribute('testCall');

			return $callTest;
		};

		$this->initResolve($function);

		$newStatus = $this->newResponse->getStatusCode();

		self::assertEquals($requestAttribut,$this->body);
		self::assertEquals(404,$newStatus);
	}

	public function testDI()
	{
		$container = new Container();

		$container['TestClass']=function (){
			return new TestClass();
		};

		$psr11 = new \Pimple\Psr11\Container($container);

		$this->resolver->setContainer($psr11);

		$function = function (){
			return $this->{"TestClass"}->doSmth();
		};

		$this->initResolve($function);

		self::assertEquals(TestClass::class." right Testresult",$this->body);
	}

	public function testWithoutReturn()
	{
		$function = function (){

		};

		$this->initResolve($function);

		self::assertEquals('',$this->body);
	}
}