<?php
namespace Gram\Test\Middleware\DummyMw;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class TestMw1 implements MiddlewareInterface
{


	public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
	{
		$words = $request->getAttribute('words',[]);

		$words[]="1";

		$request = $request->withAttribute('words',$words);

		return $handler->handle($request);
	}
}