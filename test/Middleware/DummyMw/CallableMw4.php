<?php
namespace Gram\Test\Middleware\DummyMw;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class CallableMw4
{
	public function __invoke(ServerRequestInterface $request, RequestHandlerInterface $next)
	{
		$request = $request->withAttribute('callable',"callableMws");

		$response = $next->handle($request);

		$response->getBody()->write(" at the end: mw3");

		return $response;
	}
}