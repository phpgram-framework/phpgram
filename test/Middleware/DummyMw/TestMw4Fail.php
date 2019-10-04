<?php
namespace Gram\Test\Middleware\DummyMw;


use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class TestMw4Fail
{
	public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
	{
		$words = $request->getAttribute('words',[]);

		$words[]="4fail";

		$request = $request->withAttribute('words',$words);

		return $handler->handle($request);
	}
}