<?php
namespace Gram\Test\Resolver;

use Gram\Middleware\Handler\HandlerInterface;
use Gram\Resolver\HandlerResolver;
use Gram\Test\TestClasses\TestHandlerClass;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7Server\ServerRequestCreator;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class HandlerResolverTest extends TestCase
{
	/** @var HandlerResolver */
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
		$this->resolver = new HandlerResolver();
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

	public function testResolveClassWithPsr()
	{
		$requestAttribut = 12;

		$this->request = $this->request->withAttribute('testCall',$requestAttribut);

		$handler = new TestHandlerClass();

		$this->initResolve($handler);

		$newStatus = $this->newResponse->getStatusCode();

		self::assertEquals($requestAttribut,$this->body);
		self::assertEquals(404,$newStatus);
	}
}