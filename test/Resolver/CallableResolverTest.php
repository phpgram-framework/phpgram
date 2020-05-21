<?php
namespace Gram\Test\Resolver;

use Gram\Resolver\CallableResolver;
use Gram\Test\TestClasses\CallableClass;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7Server\ServerRequestCreator;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @covers \Gram\Resolver\CallableResolver
 */
class CallableResolverTest extends TestCase
{
	/** @var CallableResolver */
	private $resolver;
	/** @var ServerRequestInterface */
	private $request;
	/** @var ResponseInterface */
	private $response;

	/** @var ResponseInterface */
	private $newResponse;

	/** @var mixed|ResponseInterface */
	private $body;

	/** @var Psr17Factory */
	private $psr17;

	protected function setUp(): void
	{
		$this->resolver = new CallableResolver();
		$this->psr17 = new Psr17Factory();
		$requestCreator = new ServerRequestCreator($this->psr17,$this->psr17,$this->psr17,$this->psr17);

		$this->request = $requestCreator->fromGlobals();
		$this->response = $this->psr17->createResponse();
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

	public function testFunction()
	{
		$function = function (ServerRequestInterface $request,ResponseInterface $response, array $args){
			return "Test erfolgreich";
		};

		$this->initResolve($function);

		self::assertEquals("Test erfolgreich",$this->body);
	}

	public function testClass()
	{
		$callable = new CallableClass();

		$this->initResolve($callable);

		self::assertEquals("test",$this->body);
	}

	public function testFunctionPsr()
	{
		$requestAttribut = "12";

		$this->request = $this->request->withAttribute('testCall',$requestAttribut);
		$this->response=$this->response->withStatus(200);

		$function = function (ServerRequestInterface $request,ResponseInterface $response, array $args){
			$callTest = $request->getAttribute('testCall');

			$response = $response->withStatus(404);
			$response->getBody()->write($callTest);

			return $response;
		};

		$this->initResolve($function);

		$newStatus = $this->body->getStatusCode();

		$body = $this->body->getBody()->__toString();

		self::assertEquals($requestAttribut,$body);
		self::assertEquals(404,$newStatus);
	}

	public function testWithoutReturn()
	{
		$function = function (){

		};

		$this->initResolve($function);

		self::assertEquals('',$this->body);
	}
}